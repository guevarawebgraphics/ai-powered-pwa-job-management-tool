<template>
    <transition name="fade">
        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50"
            @click.self="closeModal"
        >
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <!-- Modal header -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Add Black Out Dates</h2>
                    <button
                        @click="closeModal"
                        class="text-gray-600 hover:text-gray-800 focus:outline-none"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Date range inputs -->
                <div class="flex space-x-4 mb-4">
                    <div class="w-1/2">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >From</label
                        >
                        <input
                            type="date"
                            v-model="fromDate"
                            :min="todayString"
                            class="block w-full border border-gray-300 rounded-md px-2 py-1"
                            @change="handleInputChange"
                        />
                    </div>
                    <div class="w-1/2">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >To</label
                        >
                        <input
                            type="date"
                            v-model="toDate"
                            :min="todayString"
                            class="block w-full border border-gray-300 rounded-md px-2 py-1"
                            @change="handleInputChange"
                        />
                    </div>
                </div>

                <!-- Calendar section -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <button
                            class="px-2 py-1 rounded hover:bg-gray-100"
                            @click="previousMonth"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                        </button>
                        <div class="text-lg font-medium text-gray-800">
                            {{ monthName }} {{ currentYear }}
                        </div>
                        <button
                            class="px-2 py-1 rounded hover:bg-gray-100"
                            @click="nextMonth"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M9 18l6-6-6-6" />
                            </svg>
                        </button>
                    </div>
                    <!-- Days of week -->
                    <div
                        class="grid grid-cols-7 text-center font-medium text-gray-600 mb-1"
                    >
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                        <div>Sun</div>
                    </div>
                    <!-- Calendar dates -->
                    <div class="grid grid-cols-7 text-center gap-y-2">
                        <div
                            v-for="(date, index) in calendarDates"
                            :key="index"
                            class="text-sm p-1"
                            :class="{
                                'text-gray-300': !date.isCurrentMonth,
                                'cursor-pointer hover:bg-blue-100 rounded':
                                    date.isCurrentMonth && !date.disabled,
                                'bg-blue-200': date.selected,
                                'opacity-50 cursor-not-allowed': date.disabled,
                            }"
                            @click="selectDate(date)"
                        >
                            {{ date.day }}
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex justify-end space-x-2">
                    <button
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
                        @click="closeModal"
                    >
                        Cancel
                    </button>
                    <button
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        @click="saveDates"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
import axios from "axios";
import Swal from "sweetalert2"; // Import SweetAlert2

export default {
    name: "CalendarModal",
    data() {
        return {
            showModal: false,
            // Date strings in format YYYY-MM-DD
            fromDate: "",
            toDate: "",
            currentMonth: new Date().getMonth(), // 0-indexed
            currentYear: new Date().getFullYear(),
        };
    },

    mounted() {
        this.fetchUserData();
    },
    computed: {
        // Computed property to get today as YYYY-MM-DD for input min attribute.
        todayString() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, "0");
            const dd = String(today.getDate()).padStart(2, "0");
            return `${yyyy}-${mm}-${dd}`;
        },
        monthName() {
            return new Date(this.currentYear, this.currentMonth).toLocaleString(
                "default",
                {
                    month: "long",
                }
            );
        },
        calendarDates() {
            const daysInMonth = new Date(
                this.currentYear,
                this.currentMonth + 1,
                0
            ).getDate();
            // Calculate offset for Monday-first grid (JS getDay() returns 0 for Sunday)
            const firstDayOfWeek = new Date(
                this.currentYear,
                this.currentMonth,
                1
            ).getDay();
            const offset = (firstDayOfWeek + 6) % 7;
            let datesArray = [];

            // Get today's date at midnight for comparison.
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Prepare date objects for selected range.
            let start = this.fromDate ? new Date(this.fromDate) : null;
            let end = this.toDate ? new Date(this.toDate) : null;
            if (start && end && start > end) {
                [start, end] = [end, start];
            }

            // Fill placeholders for days before the current month starts.
            for (let i = 0; i < offset; i++) {
                datesArray.push({
                    day: "",
                    isCurrentMonth: false,
                    selected: false,
                    disabled: true,
                });
            }
            // Generate days for current month.
            for (let d = 1; d <= daysInMonth; d++) {
                const dateObj = new Date(
                    this.currentYear,
                    this.currentMonth,
                    d
                );
                dateObj.setHours(0, 0, 0, 0);
                let isSelected = false;
                if (start && end) {
                    isSelected = dateObj >= start && dateObj <= end;
                } else if (start) {
                    isSelected =
                        dateObj.toDateString() === start.toDateString();
                }
                // Disable the date if it's before today.
                let disabled = dateObj < today;
                datesArray.push({
                    day: d,
                    isCurrentMonth: true,
                    dateObj,
                    selected: isSelected,
                    disabled,
                });
            }
            return datesArray;
        },
    },
    methods: {
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        async saveDates() {
            console.log("From:", this.fromDate, "To:", this.toDate);

            try {
                const token = localStorage.getItem("token");
                const response = await axios.post(
                    "/api/user/update",
                    {
                        black_out_from: this.fromDate,
                        black_out_to: this.toDate,
                    },
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );

                console.log("Black Out updated successfully:", response.data);
                Swal.fire(
                    "Success",
                    "Black Out Dates updated successfully!",
                    "success"
                );
            } catch (error) {
                console.error("Error updating skills:", error);
                Swal.fire(
                    "Error",
                    "The blackout TO date must be a date after the blackout FROM date.",
                    "error"
                );
            }

            // this.closeModal();
        },
        selectDate(date) {
            // Prevent selection if the date is disabled or not in the current month.
            if (!date.isCurrentMonth || date.disabled || !date.dateObj) return;
            const clickedDateStr = date.dateObj.toISOString().split("T")[0];

            // If both dates are selected, reset and start with the clicked date.
            if (this.fromDate && this.toDate) {
                this.fromDate = clickedDateStr;
                this.toDate = "";
            } else if (this.fromDate && !this.toDate) {
                const from = new Date(this.fromDate);
                const clicked = date.dateObj;
                // If clicked date is before the current fromDate, update it.
                if (clicked < from) {
                    this.fromDate = clickedDateStr;
                } else {
                    this.toDate = clickedDateStr;
                }
            } else {
                // No date selected yet – set as fromDate.
                this.fromDate = clickedDateStr;
            }
        },
        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear += 1;
            } else {
                this.currentMonth += 1;
            }
        },
        previousMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear -= 1;
            } else {
                this.currentMonth -= 1;
            }
        },
        handleInputChange() {
            // When the input values change, the computed calendarDates automatically update.
        },
        async fetchUserData() {
            const token = localStorage.getItem("token");
            if (!token) {
                console.error("No token found in localStorage");
                return;
            }
            try {
                const response = await axios.get("/api/user", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                });

                const userData = response.data.user;

                this.fromDate = userData.black_out_from;
                this.toDate = userData.black_out_to;
            } catch (error) {
                console.error("Error fetching user data:", error);
            }
        },
    },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
