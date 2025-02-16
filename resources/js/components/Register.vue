<template>
    <div>
        <div class="w-full max-w-md space-y-6 pb-10">
            <img src="../../../public/images/logo.png" alt="Company Logo" class="w-32 mx-auto" />

            <div>

                <h1 class="text-4xl leading-[48px] text-[#232850] text-center">
                    Nice to see you!
                </h1>

                <p class="text-sm leading-[22px] font-normal text-[#9095A0] text-center">
                    Create your account
                </p>
            </div>

            <form @submit.prevent="register" class="space-y-6">
                <div class="relative">
                    <div class="relative flex items-center">
                        <i class="fas fa-user absolute left-3 text-[#221C21FF]"></i>
                        <input type="text" v-model="name" id="name" name="name" required placeholder="Enter your Full Name"
                            class="w-full px-10 py-2 mt-1 border border-[#BCC1CA] rounded-xl focus:ring focus:ring-blue-300 focus:border-blue-500" />
                    </div>
                </div>
                <div class="relative">
                    <div class="relative flex items-center">
                        <i class="fas fa-envelope absolute left-3 text-[#221C21FF]"></i>
                        <input type="email" v-model="email" id="email" name="email" required placeholder="Enter your email address"
                            class="w-full px-10 py-2 mt-1 border border-[#BCC1CA] rounded-xl focus:ring focus:ring-blue-300 focus:border-blue-500" />
                    </div>
                </div>
                <div class="relative">
                    <div class="relative flex items-center">
                        <i class="fas fa-lock absolute left-3 text-[#221C21FF]"></i>
                        <input type="password" v-model="password" id="password" name="password" required placeholder="Enter your password"
                            class="w-full px-10 py-2 mt-1 border border-[#BCC1CA] rounded-xl focus:ring focus:ring-blue-300 focus:border-blue-500" />
                    </div>
                </div>
                <div class="relative">
                    <div class="relative flex items-center">
                        <i class="fas fa-lock absolute left-3 text-[#221C21FF]"></i>
                        <input type="password" v-model="password_confirmation" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your Password"
                            class="w-full px-10 py-2 mt-1 border border-[#BCC1CA] rounded-xl focus:ring focus:ring-blue-300 focus:border-blue-500" />
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="mr-2 rounded" required>
                        <span class="text-sm text-[#221C21FF]">I agree with <a href="#"
                                class="text-blue-500 hover:underline">Terms &
                                Conditions</a></span>
                    </label>
                    <!-- <a href="#" class="text-sm text-blue-500 hover:underline text-[#221C21FF]">Forgot password?</a> -->
                </div>
                <button type="submit"
                    class="w-full px-4 py-2 font-semibold text-white bg-[#232850] rounded-xl hover:bg-[#1d2142] focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Create My Account
                </button>
            </form>
            <p class="text-sm text-center text-gray-600 mb-8">
                Already have an account? <a href="#" class="text-blue-500 hover:underline"
                    @click="redirectToLogin()">Sign In</a>
            </p>

            <!-- <p class="text-sm text-center text-gray-600">
        Don't have an account? <a href="#" class="text-blue-500 hover:underline">Sign up</a>
      </p> -->
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2
export default {
    name: "RegisterPage",
    data() {
        return {
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        };
    },
    methods: {
        redirectToLogin() {
            this.$router.push("/login");
        },
        async register() {
            try {
                await axios.get('/sanctum/csrf-cookie'); 

                const response = await axios.post('/api/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                });

                console.log('Register successful:', response.data);

                Swal.fire({
                    icon: 'success',
                    title: 'Thank you, Kindly sign-in your account',
                    text: 'Successfully Registered!',
                    timer: 2000,
                    showConfirmButton: false
                });

                // ✅ Reload page to refresh authentication state
                window.location.href = '/login';

            } catch (error) {
                console.error('Login failed:', error.response?.data || error);
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: 'Invalid email or password. Please try again.',
                });
            }
        }
    }
};
</script>


<!-- Ensure Font Awesome is included in your project -->
<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
</style>
