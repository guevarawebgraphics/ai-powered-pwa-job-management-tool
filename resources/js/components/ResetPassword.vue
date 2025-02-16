<template>
    <div>
        <div class="w-full max-w-md space-y-6">
            <img src="../../../public/images/logo.png" alt="Company Logo" class="w-32 mx-auto" />

            <div>
                <h1 class="text-4xl leading-[48px] text-[#232850] text-center">
                    Reset your Password
                </h1>

                <p class="text-sm leading-[22px] font-normal text-[#9095A0] text-center">
                    You are now verified to change your password.
                </p>
            </div>

            <!-- Remove action and use @submit.prevent for Vue handling -->
            <form action="#" class="space-y-6">
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

                <button 
                    @click.prevent="resetPassword"
                    :disabled="loading"
                    class="w-full px-4 py-2 font-semibold text-white bg-[#232850] rounded-xl hover:bg-[#1d2142] focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
                    <span v-if="!loading">Continue</span>
                    <span v-if="loading">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                    </span>
                </button>

            </form>
            <p class="text-sm text-center text-gray-600 mb-8">
            Already have an account? <a href="#" class="text-blue-500 hover:underline"
                @click="redirectToLogin()">Sign In</a>
            </p>

        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2

export default {
    name: "ForgetPage",
    data() {
        return {
            token: this.$route.query.token,  // Get token from URL
            email: this.$route.query.email,  // Get email from URL
            password: '',
            password_confirmation: '',
            loading: false, 
        };
    },
    methods: {
        redirectToLogin() {
            this.$router.push("/login");
        },
        async resetPassword() {

            
            this.loading = true;


            try {
                await axios.post('/api/forgot/reset-password', {
                    email: this.email,
                    token: this.token,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                });

                Swal.fire('Success!', 'Your password has been reset.', 'success');
                this.$router.push('/login');

            } catch (error) {
                Swal.fire('Error!', error.response?.data?.message || 'Something went wrong.', 'error');
            } finally {
                
                this.loading = false;

            }
        }
    }
};
</script>

<!-- Ensure Font Awesome is included -->
<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
</style>
