<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Profile Section -->
        <div class="flex flex-col items-center p-6">
            <div class="relative w-24 h-24">
                <!-- Profile Picture -->
                <img :src="previewPhoto" alt="Profile Picture"
                    class="w-24 h-24 rounded-md border border-gray-300 shadow-md object-cover" />

                <!-- File Input (Hidden) -->
                <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="uploadProfilePhoto" />

                <!-- Edit Button (Top Right) -->
                <button @click="triggerFileInput"
                    class="absolute top-0 right-0 bg-white border rounded-full p-1 shadow-md hover:bg-gray-100 transition">
                    <i class="fas fa-external-link-alt text-[#BCC1CAFF]"></i>
                </button>
            </div>

            <!-- Name & Title -->
            <div class="mt-3 text-center">
                <div class="relative">
                    <h2 v-if="!isEditingName" class="text-xl text-[#171A1FFF]">
                        {{ name }}
                    </h2>
                    <input v-else v-model="tempName"
                        class="border border-gray-300 rounded-md px-2 py-1 text-lg text-gray-700 focus:outline-none focus:ring focus:ring-blue-300" />

                    <!-- Edit Icon -->
                    <button v-if="!isEditingName" @click="editName" class="absolute -right-6 top-1">
                        <i class="fas fa-edit text-[#BCC1CAFF]"></i>
                    </button>
                </div>

                <div class="relative inline-block mt-1">
                    <p v-if="!isEditingTitle" class="text-[#9095A0FF]">
                        {{ professionalTitle }}
                    </p>
                    <input v-else v-model="tempTitle"
                        class="border border-gray-300 rounded-md px-2 py-1 text-sm text-gray-700 focus:outline-none focus:ring focus:ring-blue-300" />
                </div>

                <!-- Save & Cancel Buttons -->
                <div v-if="isEditingName || isEditingTitle" class="flex justify-center mt-2 space-x-2">
                    <button @click="saveChanges" class="text-green-600 hover:text-green-800">
                        <i class="fas fa-check"></i>
                        <!-- Save -->
                    </button>
                    <button @click="cancelChanges" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                        <!-- Cancel -->
                    </button>
                </div>
            </div>

            <Ratings />

            <div class="mt-6 w-full max-w-md space-y-3">
                <div v-for="(item, index) in contactInfo" :key="index"
                    class="bg-white rounded-lg shadow-sm border p-4 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <i :class="item.icon" class="text-[#171A1FFF] text-lg"></i>

                        <input v-if="item.editing" type="text" v-model="item.tempValue"
                            :placeholder="getPlaceholder(item.key)"
                            class="border border-gray-300 rounded-md px-2 py-1 text-sm text-[#171A1FFF] focus:outline-none focus:ring focus:ring-blue-300 w-full" />

                        <span v-else class="text-sm text-gray-700">
                            {{ item.value || getPlaceholder(item.key) }}
                        </span>
                    </div>

                    <div class="flex space-x-2">
                        <button v-if="item.editing" @click="saveContact(index)"
                            class="text-green-600 hover:text-green-800">
                            <i class="fas fa-check"></i>
                            <!-- Save Button -->
                        </button>
                        <button v-if="item.editing" @click="cancelEdit(index)" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                            <!-- Cancel Button -->
                        </button>
                        <button v-else @click="toggleEdit(index)" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-edit"></i>
                            <!-- Edit Button -->
                        </button>
                    </div>
                </div>
            </div>

            <!-- Schedule Section -->
            <div class="mt-6 w-full max-w-md space-y-6">
                <!-- Set Schedule -->
                <div class="bg-white rounded-lg shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-[#171A1FFF]">
                            Set Schedule
                        </h3>
                        <button class="text-gray-500 hover:text-gray-700" @click="goToSetSchedule">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div v-for="(schedule, index) in workSchedule" :key="index" class="flex justify-between">
                            <span class="text-[#505050FF] font-semibold">{{
                                schedule.day
                                }}</span>
                            <span class="text-[#505050FF]">{{
                                schedule.hours
                                }}</span>
                        </div>
                    </div>
                </div>

                <!-- Set Advanced Schedule -->
                <div
                    class="bg-white rounded-lg shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex items-center justify-between">
                    <div class="flex flex-col items-center">
                        <h3 class="text-lg font-bold text-[#171A1FFF] text-center">
                            Set Advanced Schedule
                        </h3>
                        <div class="mt-2 border-2 border-blue-300 p-3 rounded-lg" @click="showCalendarModal">
                            <i class="fas fa-calendar-check text-blue-500 text-4xl"></i>
                        </div>
                    </div>
                    <!-- Toggle Switch with label -->
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" v-model="advancedSchedule"
                            @change="updateSettings('is_blackout', advancedSchedule)" class="sr-only" />
                        <div class="relative w-10 h-5 bg-gray-300 rounded-full transition-all duration-300"
                            :class="{ 'bg-green-500': advancedSchedule }">
                            <div class="absolute left-1 top-1/2 transform -translate-y-1/2 w-3.5 h-3.5 bg-white rounded-full shadow-md transition-all duration-300"
                                :class="{ 'translate-x-5': advancedSchedule }"></div>
                        </div>
                        <span class="ml-2 font-medium text-sm text-gray-700">
                            {{ advancedSchedule ? 'On Duty' : 'Off Duty' }}
                        </span>
                    </label>
                </div>

            </div>

            <!-- Advanced Skills Section -->
            <div class="mt-6 w-full max-w-md space-y-6">
                <!-- Dropdown -->
                <!-- <div class="bg-white rounded-lg shadow-md border p-4 relative">
                    <label class="text-lg font-bold text-[#171A1FFF]">
                        Advanced Skills
                        <span class="text-gray-600 text-sm">(Select all that apply)</span>
                    </label>

                    <div class="mt-2 p-2 border border-gray-300 rounded-lg bg-white cursor-pointer flex justify-between items-center"
                        @click="
                            () => {
                                console.log('clicked');
                                toggleDropdown();
                            }
                        ">
                        <span v-if="selectedSkills.length === 0" class="text-gray-500">Select skills...</span>
                        <span v-else class="text-gray-700">{{
                            selectedSkills.join(", ")
                            }}</span>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"
                            :class="{ 'rotate-180': isDropdownOpen }"></i>
                    </div>

                    <div v-show="isDropdownOpen"
                        class="absolute left-0 right-0 top-full mt-2 bg-white border border-gray-300 rounded-lg shadow-lg z-[9999] max-h-48 overflow-y-auto"
                        style="display: block">
                        <div v-for="(skill, index) in skills" :key="index"
                            class="p-3 flex items-center space-x-2 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" :id="'skill_' + index" v-model="selectedSkills" :value="skill"
                                @change="updateSkills" class="w-4 h-4 text-blue-500 border-gray-300 rounded" />
                            <label :for="'skill_' + index" class="text-gray-700 cursor-pointer">{{ skill }}</label>
                        </div>
                    </div>
                </div> -->

                <!-- Toggle Options -->
                <div class="bg-white rounded-lg shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-[#171A1FFF]">Notifications</span>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" v-model="notificationsEnabled" @change="
                                    updateSettings(
                                        'is_notify',
                                        notificationsEnabled
                                    )
                                " class="sr-only" />
                            <div class="relative w-10 h-5 bg-gray-300 rounded-full transition-all duration-300" :class="{
                                    'bg-green-500': notificationsEnabled,
                                }">
                                <div class="absolute left-1 top-1/2 -translate-y-1/2
               w-3.5 h-3.5 bg-white rounded-full
               shadow-md shadow-[#171a1f17] drop-shadow-sm
               transition-all duration-300" :class="{
                                        'translate-x-5': notificationsEnabled,
                                    }"></div>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-between items-center mt-3">
                        <span class="text-lg font-bold text-[#171A1FFF]">Location Services</span>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" v-model="locationEnabled" @change="
                                    updateSettings(
                                        'is_location',
                                        locationEnabled
                                    )
                                " class="sr-only" />
                            <div class="relative w-10 h-5 bg-gray-300 rounded-full transition-all duration-300"
                                :class="{ 'bg-green-500': locationEnabled }">
                                <div class="absolute left-1 top-1/2 -translate-y-1/2
               w-3.5 h-3.5 bg-white rounded-full
               shadow-md shadow-[#171a1f17] drop-shadow-sm
               transition-all duration-300" :class="{
                                        'translate-x-5': locationEnabled,
                                    }"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Chat for Help Button -->
                <div class="cursor-pointer bg-white rounded-lg shadow-md border p-4 flex justify-between items-center">
                    <span class="text-lg font-bold text-[#171A1FFF]">Open a Chat for Help</span>
                    <div class="border-2 border-blue-400 p-3 rounded-lg" @click="openChat()">
                        <i class="fas fa-comment text-blue-500 text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <BottomNav />
    </div>

    <!-- Chat Modal -->
    <div v-if="isChatOpen" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center"
        @click="closeChat">
        <div class="bg-white w-96 rounded-lg shadow-lg" @click.stop>
            <!-- Modal Header -->
            <div class="flex justify-between items-center bg-blue-500 text-white p-4 rounded-t-lg">
                <h2 class="text-lg font-bold">Chat Support</h2>
                <button @click="closeChat" class="text-white text-xl">
                    &times;
                </button>
            </div>

            <!-- Chat Body -->
            <div class="p-4 h-64 overflow-y-auto" ref="chatBox">
                <div v-for="(message, index) in chatMessages" :key="index" class="flex items-start mb-3" :class="{
                        'justify-end': message.type === 'sender',
                        'justify-start': message.type === 'receiver',
                    }">
                    <!-- Profile Picture -->
                    <img :src="message.avatar" alt="Profile" class="w-8 h-8 rounded-full" :class="{
                            'order-2 ml-2': message.type === 'sender',
                            'mr-2': message.type === 'receiver',
                        }" />

                    <!-- Message Content -->
                    <div>
                        <p class="text-xs font-bold" :class="{
                                'text-right': message.type === 'sender',
                                'text-left': message.type === 'receiver',
                            }">
                            {{ message.name }}
                        </p>
                        <div :class="
                                message.type === 'sender'
                                    ? 'bg-[#F3F4F6FF] text-[#171A1FFF]'
                                    : 'bg-[#E5EDF8FF] text[#171A1FFF]'
                            " class="p-2 rounded-lg inline-block max-w-xs">
                            {{ message.text }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="p-4 border-t flex">
                <input v-model="newMessage" @keyup.enter="sendMessage" type="text" class="w-full p-2 border rounded-lg"
                    placeholder="Type your message..." />
                <button @click="sendMessage" class="ml-2 bg-blue-500 text-white p-2 rounded-lg">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Include the modal component with a ref -->
    <CalendarModal ref="calendarModal" />
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import Ratings from "../sections/Ratings.vue";
import CalendarModal from "../sections/CalendarModal.vue";
import axios from "axios";
import Swal from "sweetalert2"; // Import SweetAlert2
import { messaging } from "../../firebase";
import { getToken, onMessage } from "firebase/messaging";
import { mapState } from "vuex";

export default {
    components: { NavBar, BottomNav, Ratings, CalendarModal },
    name: "ProfilePage",
    computed: {
        ...mapState(["chatMessages"]),
    },
    data() {
        return {
            skills: [
                "List Item 1",
                "List Item  2",
                "List Item  3",
                "List Item  4",
            ],
            selectedSkills: [], // Store multiple selected items
            isDropdownOpen: false, // Toggle dropdown visibility
            notificationsEnabled: false,
            locationEnabled: false,
            previewPhoto: "/images/avatar.png",
            name: "--",
            professionalTitle: "--",
            tempName: "",
            tempTitle: "",
            isEditingName: false,
            isEditingTitle: false,
            locationEnabled: false,
            contactInfo: [
                {
                    icon: "fas fa-building",
                    value: "",
                    tempValue: "",
                    key: "mobile_no",
                    editing: false,
                },
                {
                    icon: "fas fa-home",
                    value: "",
                    tempValue: "",
                    key: "home_no",
                    editing: false,
                },
                {
                    icon: "fas fa-at",
                    value: "",
                    tempValue: "",
                    key: "email",
                    editing: false,
                },
                {
                    icon: "fas fa-map-marker-alt",
                    value: "",
                    tempValue: "",
                    key: "current_address",
                    editing: false,
                },
                {
                    icon: "fa-regular fa-map",
                    value: "",
                    tempValue: "",
                    key: "service_area",
                    editing: false,
                },
            ],
            workSchedule: [],
            advancedSchedule: false,
            isChatOpen: false,
            newMessage: "",
            messages: [],
            accountProfile: [],
        };
    },
    methods: {
        getPlaceholder(key) {
            const placeholders = {
                mobile_no: "Enter Mobile Number",
                home_no: "Enter Home Number",
                email: "Enter Email Address",
                current_address: "Enter Current Address",
                service_area: "Enter Service Area",
            };
            return placeholders[key] || "Enter Information";
        },

        async updateSettings(field, value) {
            try {
                const token = localStorage.getItem("token");
                const response = await fetch("/api/user/update", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`, // If using authentication
                    },
                    body: JSON.stringify({
                        [field]: value ? 1 : 0,
                    }),
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(
                        data.message || "Failed to update settings"
                    );
                }
                console.log(`${field} updated successfully`);
            } catch (error) {
                console.error(`Error updating ${field}:`, error.message);
            }
        },

        editName() {
            this.tempName = this.name;
            this.tempTitle = this.professionalTitle;
            this.isEditingName = true;
            this.isEditingTitle = true;
        },

        async saveChanges() {
            const token = localStorage.getItem("token");
            try {
                const response = await axios.post(
                    "/api/user/update",
                    {
                        name: this.tempName,
                        professional_title: this.tempTitle,
                    },
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );

                this.name = this.tempName;
                this.professionalTitle = this.tempTitle;
                this.isEditingName = false;
                this.isEditingTitle = false;

                console.log("Updated successfully:", response.data);
            } catch (error) {
                console.error("Error updating:", error);
            }
        },

        cancelChanges() {
            this.isEditingName = false;
            this.isEditingTitle = false;
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

                // Check if profile_photo exists and is valid
                if (
                    userData.profile_photo &&
                    userData.profile_photo !== "null"
                ) {
                    this.previewPhoto = userData.profile_photo.startsWith(
                        "http"
                    )
                        ? userData.profile_photo
                        : `${process.env.VUE_APP_BASE_URL}/storage/${userData.profile_photo}`;
                } else {
                    this.previewPhoto = "/images/avatar.png";
                }

                // Set user info
                this.name = userData.name;

                this.accountProfile = userData;
                this.professionalTitle = userData.professional_title;
                this.selectedSkills = userData.skills
                    ? userData.skills.split(",")
                    : [];

                this.$nextTick(() => {
                    // Convert "0"/"1" to Boolean

                    this.advancedSchedule = userData.is_blackout === "1";
                    this.notificationsEnabled = userData.is_notify === "1";
                    this.locationEnabled = userData.is_location === "1";
                });

                console.log("Fetched user data:", response);

                // Populate other contact info fields
                this.contactInfo.forEach((item) => {
                    if (userData[item.key]) {
                        item.value = userData[item.key];
                    }
                });
            } catch (error) {
                console.error("Error fetching user data:", error);
            }
        },

        toggleEdit(index) {
            this.contactInfo[index].editing = true;
            this.contactInfo[index].tempValue = this.contactInfo[index].value; // Store original value
        },

        async saveContact(index) {
            const item = this.contactInfo[index];
            const token = localStorage.getItem("token");

            console.log("Sending data:", { [item.key]: item.tempValue }); // Debug log

            try {
                const response = await axios.post(
                    "/api/user/update",
                    { [item.key]: item.tempValue },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "application/json",
                        },
                    }
                );

                item.value = item.tempValue; // Update displayed value
                item.editing = false; // Exit edit mode
                console.log("Updated successfully:", response.data);
            } catch (error) {
                console.error("Update failed:", error);
                Swal.fire("Error", "Failed to update record!", "error");
            }
        },

        cancelEdit(index) {
            this.contactInfo[index].tempValue = this.contactInfo[index].value; // Revert changes
            this.contactInfo[index].editing = false; // Hide edit mode
        },

        // Update contact info in real-time
        updateContact(index) {
            const field = this.contactInfo[index].key;
            const value = this.contactInfo[index].value;
            const token = localStorage.getItem("token"); // Fetch token from localStorage

            axios
                .post(
                    "/api/user/update",
                    { [field]: value },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "application/json",
                        },
                    }
                )
                .then((response) => {
                    console.log("Updated successfully:", response.data);
                })
                .catch((error) => {
                    console.error("Update failed:", error);
                });
        },

        toggleDropdown() {
            this.isDropdownOpen = !this.isDropdownOpen;
            console.log(this.isDropdownOpen);
        },

        closeDropdown(event) {
            if (!this.$el.contains(event.target)) {
                this.isDropdownOpen = false;
            }
        },

        triggerFileInput() {
            this.$refs.fileInput.click(); // Open file input
        },
        async uploadProfilePhoto(event) {
            const file = event.target.files[0];

            if (!file || !file.type.startsWith("image/")) {
                console.error("Invalid file selected. Please choose an image.");
                return;
            }

            // Create a temporary preview before uploading
            if (this.previewPhoto) {
                URL.revokeObjectURL(this.previewPhoto); // Revoke previous URL to free memory
            }
            this.previewPhoto = URL.createObjectURL(file);

            const formData = new FormData();
            formData.append("profile_photo", file);

            const token = localStorage.getItem("token");

            try {
                const response = await axios.post(
                    "/api/user/update",
                    formData,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                // Update with the actual image URL from the server response
                // this.previewPhoto = response.data.profile_photo;
                console.log(
                    "Profile photo updated successfully:",
                    response.data
                );
            } catch (error) {
                console.error("Error updating profile photo:", error);
            }
        },
        async updateSkills() {
            try {
                const token = localStorage.getItem("token");
                const response = await axios.post(
                    "/api/user/update",
                    {
                        skills: this.selectedSkills, // Send array to backend
                    },
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );

                console.log("Skills updated successfully:", response.data);
                // Swal.fire("Success", "Skills updated successfully!", "success");
            } catch (error) {
                console.error("Error updating skills:", error);
                Swal.fire("Error", "Failed to update skills!", "error");
            }
        },
        goToSetSchedule() {
            this.$router.push("/set-schedule"); // Redirect to Set Schedule page
        },
        async fetchSchedule() {
            const token = localStorage.getItem("token"); // Assuming auth token is needed
            try {
                const response = await axios.get("/api/schedule", {
                    headers: { Authorization: `Bearer ${token}` },
                });

                // Map API response to the format needed for display
                const daysMap = {
                    1: "Sunday",
                    2: "Monday",
                    3: "Tuesday",
                    4: "Wednesday",
                    5: "Thursday",
                    6: "Friday",
                    7: "Saturday",
                };

                const scheduleMap = {};

                // Initialize all days as "Closed"
                Object.keys(daysMap).forEach((day) => {
                    scheduleMap[day] = { day: daysMap[day], hours: "Closed" };
                });

                // Process API response
                response.data.forEach((entry) => {
                    if (entry.is_close === 0) {
                        const dayLabel = daysMap[entry.day];
                        if (
                            !scheduleMap[entry.day].hours ||
                            scheduleMap[entry.day].hours === "Closed"
                        ) {
                            scheduleMap[
                                entry.day
                            ].hours = `${entry.open} - ${entry.close}`;
                        } else {
                            scheduleMap[
                                entry.day
                            ].hours += `, ${entry.open} - ${entry.close}`;
                        }
                    }
                });

                this.workSchedule = Object.values(scheduleMap);
            } catch (error) {
                console.error("Error fetching schedule:", error);
            }
        },
        openChat() {
            this.isChatOpen = true;
            this.getMessage();
        },
        closeChat() {
            this.isChatOpen = false;
        },
        async getMessage() {
            const tokenx = localStorage.getItem("token");
            const loggedInUserId = this.accountProfile.id; // Get the logged-in user ID

            await axios
                .post(
                    "/api/chat/listings",
                    {
                        from_user_id: loggedInUserId,
                    },
                    {
                        headers: {
                            // 'Authorization': `Bearer ${token}`,
                            "Content-Type": "application/json",
                        },
                    }
                )
                .then((response) => {
                    console.log("successfully:", response);
                    this.messages = response.data.data.map((msg) => {
                        const isSentByUser = msg.from_user_id == loggedInUserId; // Determine if message is from the user

                        return {
                            id: msg.id,
                            type: isSentByUser ? "sender" : "receiver",
                            sender_id: msg.from_user_id,
                            receiver_id: msg.to_user_id,
                            name: isSentByUser ? "You" : msg.sender.name, // Fix: Display "You" instead of sender's name
                            text: msg.message,
                            isSent: isSentByUser, // True if the message was sent by the user
                            avatar: isSentByUser
                                ? this.accountProfile.profile_photo ||
                                  "https://randomuser.me/api/portraits/men/20.jpg"
                                : msg.sender.profile_photo ||
                                  `https://theguild.appliancerepairamerican.com/public/images/logo.png`,
                        };
                    });

                    this.$store.commit("setChatMessages", this.messages);

                    this.$nextTick(() => {
                        this.$refs.chatBox.scrollTop =
                            this.$refs.chatBox.scrollHeight;
                    });
                })
                .catch((error) => {
                    console.error("failed:", error);
                });
        },
        sendMessage() {
            const token = localStorage.getItem("token");
            const loggedInUserId = this.accountProfile.id;
            const admin_api_endpoint = import.meta.env.VITE_API_ENDPOINT_ADMIN;
            // axios.post('/api/chat/store',
            axios
                .post(
                    `${admin_api_endpoint}/api/store/chat`,
                    {
                        from_user_id: loggedInUserId,
                        message: this.newMessage,
                        role_id: this.accountProfile.role_id,
                    },
                    {
                        headers: {
                            // 'Authorization': `Bearer ${token}`,
                            "Content-Type": "application/json",
                        },
                    }
                )
                .then((response) => {
                    if (this.newMessage.trim() !== "") {
                        const newMsg = {
                            sender: "sender",
                            name: this.accountProfile.name,
                            text: this.newMessage.trim(),
                            avatar: this.accountProfile.profile_photo,
                        };

                        // Commit the message to Vuex
                        this.$store.commit("addChatMessage", newMsg);

                        // Clear the input field
                        this.newMessage = "";
                    }

                    // this.getMessage(); // Refresh chat after sending
                    console.log("success:", response);
                })
                .catch((error) => {
                    console.error("failed:", error);
                });
        },
        showCalendarModal() {
            // Opens the modal by calling the openModal() method on the CalendarModal component
            this.$refs.calendarModal.openModal();
        },
    },
    mounted() {
        this.fetchSchedule();
        this.fetchUserData();
        document.addEventListener("click", this.closeDropdown);
        // window.Echo.channel("notifications") // Public Channel (No Auth)
        //     .listen("NewNotificationEvent", (event) => {
        //         this.getMessage();
        //     });
    },
    beforeUnmount() {
        document.removeEventListener("click", this.closeDropdown);
    },
};
</script>
