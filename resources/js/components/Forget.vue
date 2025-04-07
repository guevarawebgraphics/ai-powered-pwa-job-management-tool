<template>
    <div>
        <div class="w-full max-w-md space-y-6">
            <img src="../../../public/images/logo.png" alt="Company Logo" class="w-32 mx-auto" />

            <div>
                <h1 class="text-4xl leading-[48px] text-[#232850] text-center">
                    Forgot Password
                </h1>

                <p class="text-sm leading-[22px] font-normal text-[#9095A0] text-center">
                    We'll verify your account & send reset password link
                </p>
            </div>

            <!-- Remove action and use @submit.prevent for Vue handling -->
            <form action="#" class="space-y-6">
                <div class="relative">
                    <div class="relative flex items-center">
                        <i class="fas fa-envelope absolute left-3 text-[#221C21FF]"></i>
                        <input v-model="email" type="email" required placeholder="Enter your email address"
                            class="w-full px-10 py-2 mt-1 border border-[#BCC1CA] rounded-xl focus:ring focus:ring-blue-300 focus:border-blue-500" />
                    </div>
                </div>

                <!-- <button type="submit"
                    class="w-full px-4 py-2 font-semibold text-white bg-[#232850] rounded-xl hover:bg-[#1d2142] focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Continue
                </button> -->


                <button 
                    @click.prevent="forget"
                    :disabled="loading"
                    class="w-full px-4 py-2 font-semibold text-white bg-[#232850] rounded-xl hover:bg-[#1d2142] focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
                    <span v-if="!loading">Continue</span>
                    <span v-if="loading">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Sending...
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
            email: '',
            loading: false, 
        };
    },
    methods: {
        redirectToLogin() {
            this.$router.push("/login");
        },
        async forget() {

            
            this.loading = true;

            try {
                await axios.get('/sanctum/csrf-cookie'); 

                const response = await axios.post('/api/forgot/password-link', {
                    email: this.email,
                });

                console.log(response);

                Swal.fire({
                    icon: 'success',
                    title: 'Reset password link successfully sent!',
                    text: 'Success',
                    timer: 2000,
                    showConfirmButton: false
                });


                this.$router.push("/login");

            } catch (error) {
                console.error('Forgot failed:', error.response?.data || error);
                Swal.fire({
                    icon: 'error',
                    title: 'Forgot password Failed',
                    text: 'Invalid email. Please try again.',
                });
            } finally {
                this.loading = false; // ✅ Stop loading
            }
        }
    }
};
</script>

<!-- Ensure Font Awesome is included -->
<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
</style>
