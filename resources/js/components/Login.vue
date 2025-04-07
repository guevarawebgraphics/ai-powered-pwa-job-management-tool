<template>
    <div>
        <div class="w-full max-w-md space-y-6">
            <img src="../../../public/images/logo.png" alt="Company Logo" class="w-32 mx-auto" />

            <div>
                <h1 class="text-4xl leading-[48px] text-[#232850] text-center">
                    Hello Again!
                </h1>

                <p class="text-sm leading-[22px] font-normal text-[#9095A0] text-center">
                    Log into your account
                </p>
            </div>

            <!-- Remove action and use @submit.prevent for Vue handling -->
            <form action="#" class="space-y-6">
                <div class="relative">
                    <div class="relative flex items-center">
                        <i class="fas fa-envelope absolute left-3 text-[#221C21FF]"></i>
                        <input v-model="email" type="email" required placeholder="Enter your email address"
                            @blur="email = email.trim().toLowerCase()"
                            class="w-full px-10 py-2 mt-1 border border-[#BCC1CA] rounded-xl focus:ring focus:ring-blue-300 focus:border-blue-500" />
                    </div>
                </div>
                <div class="relative">
                    <div class="relative flex items-center">
                        <i class="fas fa-lock absolute left-3 text-[#221C21FF]"></i>
                        <input v-model="password" type="password" required placeholder="Enter your password"
                            class="w-full px-10 py-2 mt-1 border border-[#BCC1CA] rounded-xl focus:ring focus:ring-blue-300 focus:border-blue-500" />
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <a href="#" @click="redirectToForgot()"
                        class="text-sm text-blue-500 hover:underline text-[#221C21FF]">Forgot password?</a>
                </div>
                <!-- <button type="submit"
                    class="w-full px-4 py-2 font-semibold text-white bg-[#232850] rounded-xl hover:bg-[#1d2142] focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Continue
                </button> -->

                <button @click.prevent="login" :disabled="loading"
                    class="w-full px-4 py-2 font-semibold text-white bg-[#232850] rounded-xl hover:bg-[#1d2142] focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <span v-if="!loading">Continue</span>
                    <span v-if="loading">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                    </span>
                </button>
            </form>
            <p class="text-sm text-center text-gray-600">
                Don't have an account? <a href="#" class="text-blue-500 hover:underline"
                    @click="redirectToRegister()">Sign up</a>
            </p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2

export default {
    name: "LoginPage",
    data() {
        return {
            email: '',
            password: '',
            loading: false, 
        };
    },
    methods: {
        async login() {

            
            this.loading = true;
            // Ensure email is trimmed and in lowercase
            this.email = this.email.trim().toLowerCase();
            
            try {
                await axios.get('/sanctum/csrf-cookie'); 

                const response = await axios.post('/api/login', {
                    email: this.email,
                    password: this.password,
                });

                console.log('Login successful:', response.data);

                // ✅ Save token to localStorage
                localStorage.setItem('token', response.data.token);

                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: 'Welcome back!',
                    timer: 2000,
                    showConfirmButton: false
                });

                // ✅ Reload page to refresh authentication state
                window.location.href = '/dashboard';

            } catch (error) {
                console.error('Login failed:', error.response?.data || error);
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: 'Invalid email or password. Please try again.',
                });
            } finally {
                
                this.loading = false;

            }
        },
        redirectToRegister() {
            this.$router.push("/register");
        },
        redirectToForgot() {
            this.$router.push("/forgot-password");
        }
    }
};
</script>

<!-- Ensure Font Awesome is included -->
<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
</style>
