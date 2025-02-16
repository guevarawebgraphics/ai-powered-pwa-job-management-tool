<template> 
    <div class="mt-20 mb-20">
        <NavBar />

        <div class="max-w-lg mx-auto p-6">
            <h1 class="text-2xl text-center text-[#171A1FFF] mb-6">Set Your Hours</h1>

            <form @submit.prevent="submitForm">
                <div v-for="(day, index) in schedule" :key="index" class="mb-6">
                    <!-- Day Header -->
                    <div class="items-center space-x-2">
                        <h2 class="text-normal text-[#171A1FFF]">{{ day.name }}</h2>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" v-model="day.closed" class="peer hidden">
                            <div class="w-6 h-6 flex items-center justify-center rounded-[8px] border-2 border-[#43474AFF] bg-white peer-checked:bg-white peer-checked:border-[#43474AFF] transition-all">
                                <svg v-if="day.closed" class="w-4 h-4 text-[#43474AFF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <span class="text-[#43474AFF]">Closed</span>
                        </label>
                    </div>

                    <!-- Time Inputs -->
                    <div v-if="!day.closed" class="mt-3 space-y-3">
                        <div v-for="(slot, slotIndex) in day.slots" :key="slotIndex" class="space-y-1">
                            <label class="text-sm text-[#43474AFF]">Start</label>
                            <input type="time" v-model="slot.open" class="w-full p-2 border border-[#43474AFF] rounded-md focus:ring-2 focus:ring-blue-300 focus:outline-none">

                            <label class="text-sm text-[#43474AFF]">End</label>
                            <input type="time" v-model="slot.close" class="w-full p-2 border border-[#43474AFF] rounded-md focus:ring-2 focus:ring-blue-300 focus:outline-none">
                        </div>

                        <!-- Add Time Slot Button -->
                        <button type="button" @click="addSlot(index)" class="mt-2 flex items-center text-[#43474AFF]">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">
                    Submit
                </button>
            </form>
        </div>

        <BottomNav />
    </div>
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import axios from "axios";
import Swal from 'sweetalert2'; // Import SweetAlert2

export default {
    components: { NavBar, BottomNav },
    name: "SetSchedulePage",
    data() {
        return {
            // schedule: [
            //     { name: "Sunday", closed: true, slots: [{ open: "9:00 AM", close: "5:00 PM" }] },
            //     { name: "Monday", closed: false, slots: [{ open: "8:00 AM", close: "8:00 PM" }] },
            //     { name: "Tuesday", closed: false, slots: [{ open: "8:00 AM", close: "8:00 PM" }] },
            //     { name: "Wednesday", closed: true, slots: [{ open: "8:00 AM", close: "8:00 PM" }] },
            //     { name: "Thursday", closed: false, slots: [{ open: "8:00 AM", close: "8:00 PM" }] },
            //     { name: "Friday", closed: false, slots: [{ open: "8:00 AM", close: "8:00 PM" }] },
            //     { name: "Saturday", closed: false, slots: [{ open: "9:00 AM", close: "5:00 PM" }] },
            // ],
            schedule: [],
            dayMapping: {
                1: "Monday",
                2: "Tuesday",
                3: "Wednesday",
                4: "Thursday",
                5: "Friday",
                6: "Saturday",
                7: "Sunday",
            },
        };
    },
    methods: {
        async fetchSchedule() {
            try {
                const token = localStorage.getItem("token");
                const response = await axios.get("/api/schedule", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                });

                const apiData = response.data;

                // Initialize all days as closed by default
                const scheduleMap = {};
                Object.entries(this.dayMapping).forEach(([dayNumber, dayName]) => {
                    scheduleMap[dayName] = {
                        name: dayName,
                        closed: true, // Default to closed
                        slots: [],
                    };
                });

                // Process fetched schedule data
                apiData.forEach((item) => {
                    const dayName = this.dayMapping[item.day];

                    if (!dayName) return; // Skip invalid day numbers

                    // If the day has an open schedule, mark it as open
                    if (item.is_close === 0) {
                        scheduleMap[dayName].closed = false;
                        scheduleMap[dayName].slots.push({
                            open: item.open,
                            close: item.close,
                        });
                    }
                });

                // Convert object to array
                this.schedule = Object.values(scheduleMap);
                console.log("Updated Schedule:", this.schedule);
            } catch (error) {
                console.error("Error fetching schedule:", error);
            }
        },
        addSlot(index) {
            this.schedule[index].slots.push({ open: "", close: "" });
        },
        async submitForm() {
            try {

                const token = localStorage.getItem('token'); 

                const response = await axios.post('/api/schedule/store', 
                { schedule: this.schedule }, 
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                console.log('response: ' , response);

                Swal.fire({
                    icon: 'success',
                    title: 'Successfully stored',
                    text:'Schedule has been successfully updated!',
                    timer: 2000,
                    showConfirmButton: false
                });
                
            } catch (error) {
                console.error("Error saving schedule:", error);
                
                if (error.response && error.response.status === 422) {
                    // Capture Laravel validation errors
                    this.errors = error.response.data.errors;

                    // Format errors into a readable list
                    let errorMessages = Object.values(this.errors).map(err => `• ${err[0]}`).join('<br>');

                    // Show errors in SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors',
                        html: errorMessages, // Use 'html' to support line breaks
                        confirmButtonText: 'OK'
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Store Failed',
                        text: 'Something went wrong!',
                    });
                }
            }
        },
    },
    async mounted() {
        await this.fetchSchedule();
    }
};
</script>
