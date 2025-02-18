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
        <div class="relative flex space-x-6">  <!-- Increased spacing -->
            <button class="text-gray-800 text-2xl hover:text-gray-600">
                <i class="fa-solid fa-circle-plus text-[#171A1FFF]"></i>
            </button>
            <button @click="goToNotification" class="text-gray-800 text-2xl hover:text-gray-600">
                <i class="fa-regular fa-bell text-[#171A1FFF]"></i>
            </button>

            <!-- Settings Dropdown -->
            <div class="relative ml-2">  <!-- Adds extra margin to push dropdown slightly right -->
                <button @click="toggleDropdown" class="text-gray-800 text-2xl hover:text-gray-600 focus:outline-none">
                    <i class="fas fa-cog text-[#6b6c70]"></i>
                </button>

                <div v-if="dropdownOpen"
                    class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg">
                    <button @click="goToProfile"
                        class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i> Profile
                    </button>
                    <button @click="logout"
                        class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
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

export default {
    name: "NavBar",
    data() {
        return {
            dropdownOpen: false
        };
    },
    methods: {
        goBack() {
            if (this.$route.path === "/set-schedule") {
                this.$router.push("/profile"); // Redirect to profile if on /set-schedule
            } else {
                this.$router.go(-1); // Default behavior: go back
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
            this.dropdownOpen = false; // Close dropdown after navigation
        },
        async logout() {
            try {
                const token = localStorage.getItem('token'); // ✅ Retrieve token from localStorage

                if (!token) {
                    throw new Error('No authentication token found');
                }

                await axios.post('/api/logout', {}, {
                    headers: {
                        Authorization: `Bearer ${token}` // ✅ Send token in Authorization header
                    }
                });

                // ✅ Clear token from localStorage
                localStorage.removeItem('token');

                Swal.fire({
                    icon: 'success',
                    title: 'Logged Out',
                    text: 'You have been successfully logged out.',
                    timer: 2000,
                    showConfirmButton: false
                });

                // ✅ Redirect to login page
                window.location.href = '/login';

            } catch (error) {
                console.error('Logout failed:', error.response?.data || error);
                Swal.fire({
                    icon: 'error',
                    title: 'Logout Failed',
                    text: 'An error occurred. Please try again.',
                });
            }
        }


    }
};
</script>

<style scoped>
/* Optional: Adjust padding to avoid content being covered by navbar */
.content {
    padding-top: 4rem;
}

/* Ensure dropdown doesn't disappear immediately */
.relative:hover .absolute {
    display: block;
}
</style>
