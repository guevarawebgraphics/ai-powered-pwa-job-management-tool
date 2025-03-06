<template>
    <nav class="fixed top-0 left-0 w-full flex items-center justify-between px-4 py-2 shadow-md bg-white z-50">
        <!-- Back Button -->
        <button @click="goBack" class="text-gray-800 text-2xl">
            <i class="fas fa-arrow-left"></i>
        </button>

        <!-- Centered Logo -->
        <div class="absolute left-1/2 transform -translate-x-1/2">
            <img src="../../../../public/images/logo.png" alt="Logo" class="w-12 h-12" />
        </div>

        <!-- Right Icons -->
        <div class="relative flex space-x-6">
            <button class="text-gray-800 text-2xl hover:text-gray-600">
                <i class="fa-solid fa-circle-plus text-[#171A1FFF]"></i>
            </button>

            <!-- 🔔 Notification Bell with Badge -->
            <button @click="goToNotification" class="relative text-gray-800 text-2xl hover:text-gray-600">
                <i class="fa-regular fa-bell text-[#171A1FFF]"></i>

                <!-- Red Badge for Unseen Notifications -->
                <span v-if="unseenCount > 0"
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full px-2 py-0.5">
                    {{ unseenCount }}
                </span>
            </button>

            <!-- Settings Dropdown -->
            <div class="relative ml-2">
                <button @click="toggleDropdown" class="text-gray-800 text-2xl hover:text-gray-600 focus:outline-none">
                    <i class="fas fa-cog text-[#6b6c70]"></i>
                </button>

                <div v-if="dropdownOpen"
                    class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg">
                    <button @click="goToProfile"
                        class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i> Profile
                    </button>
                    <button @click="logout" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import '/resources/js/echo.js';

export default {
    name: "NavBar",
    data() {
        return {
            dropdownOpen: false,
            notifications: [], // Store multiple notifications
            unseenCount: 0 // 🔴 Store unseen notification count
        };
    },
    methods: {
        goBack() {
            if (this.$route.path === "/set-schedule") {
                this.$router.push("/profile");
            } else {
                this.$router.go(-1);
            }
        },
        goToNotification() {
            this.$router.push('/notification');
        },
        toggleDropdown() {
            this.dropdownOpen = !this.dropdownOpen;
        },
        goToProfile() {
            this.$router.push('/profile');
            this.dropdownOpen = false;
        },
        async logout() {
            try {
                const token = localStorage.getItem('token');

                if (!token) {
                    throw new Error('No authentication token found');
                }

                await axios.post('/api/logout', {}, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                localStorage.removeItem('token');

                Swal.fire({
                    icon: 'success',
                    title: 'Logged Out',
                    text: 'You have been successfully logged out.',
                    timer: 2000,
                    showConfirmButton: false
                });

                window.location.href = '/login';
            } catch (error) {
                console.error('Logout failed:', error.response?.data || error);
                Swal.fire({
                    icon: 'error',
                    title: 'Logout Failed',
                    text: 'An error occurred. Please try again.',
                });
            }
        },

        /** 🔴 Fetch unseen notification count */
        async fetchUnseenCount() {
            try {
                const userId = 3; // Replace with dynamic user ID if needed
                const response = await axios.get(`/api/notify/get/${userId}/unseen`);

                if (response.data && response.data.count !== undefined) {
                    this.unseenCount = response.data.count;
                }
            } catch (error) {
                console.error("❌ Failed to fetch unseen notification count:", error);
            }
        }
    },
    async mounted() {
        await this.fetchUnseenCount(); // Fetch unseen notifications count on mount

        // Listen for real-time notifications
        window.Echo.channel('notifications')
            .listen('NewNotificationEvent', (event) => {
                console.log('🔔 New notification received:', event);

                // Update unseen notification count dynamically
                this.unseenCount++;

                // Extract the message content
                const content = event.message?.content || 'No content';
                const title = event.message?.name || 'New Notification';

                const newNotification = {
                    id: Date.now(),
                    title: title,
                    message: content
                };

                this.notifications.push(newNotification);

                // Auto-remove after 5 seconds
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== newNotification.id);
                }, 5000);


                this.fetchUnseenCount(); 
            });


        window.Echo.channel('notifications')
        .listen('SeenNotificationEvent', (event) => {
            this.fetchUnseenCount(); 
        });
    }
};
</script>

<style scoped>
/* Ensure dropdown doesn't disappear immediately */
.relative:hover .absolute {
    display: block;
}
</style>
