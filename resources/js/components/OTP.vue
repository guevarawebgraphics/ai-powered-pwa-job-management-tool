<template>
    <div>
        <div class="w-full max-w-md p-8 space-y-6">

            <!-- Back Button -->
            <button @click="logout" class="absolute left-4 top-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-lg"></i>
            </button>

            <img src="../../../public/images/logo.png" alt="Company Logo" class="w-32 mx-auto" />

            <div>
                <h1 class="text-4xl leading-[48px] font-bold text-[#232850] text-center">
                    Verify your email
                </h1>

                <p class="text-sm leading-[22px] font-normal text-[#9095A0] text-center">
                    Please enter the verification code <b>we sent to your email address</b> to complete the verification
                    process.
                </p>
            </div>

            <form action="#" method="POST" class="space-y-6">
                <!-- OTP Input -->
                <div class="flex justify-center space-x-2">
                    <!-- <input type="text" v-model="otp[0]" maxlength="1" class="otp-input" ref="otp0"
                        @input="focusNext(0)">
                    <input type="text" v-model="otp[1]" maxlength="1" class="otp-input" ref="otp1"
                        @input="focusNext(1)">
                    <input type="text" v-model="otp[2]" maxlength="1" class="otp-input" ref="otp2"
                        @input="focusNext(2)">
                    <input type="text" v-model="otp[3]" maxlength="1" class="otp-input" ref="otp3"
                        @input="focusNext(3)">
                    <input type="text" v-model="otp[4]" maxlength="1" class="otp-input" ref="otp4"
                        @input="focusNext(4)">
                    <input type="text" v-model="otp[5]" maxlength="1" class="otp-input" ref="otp5"
                        @input="focusNext(5)"> -->

                        <input 
                            v-for="(digit, index) in otp" 
                            :key="index" 
                            type="text" 
                            v-model="otp[index]" 
                            maxlength="1" 
                            class="otp-input" 
                            ref="otpInput" 
                            :class="otpBorderClass"
                            @input="focusNext(index)"
                        />

                </div>

                <!-- Error Message -->
                <!-- <p v-if="errorMessage" class="text-sm text-red-500 text-center flex items-center justify-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ errorMessage }}
                </p> -->

                <!-- Resend Code Button -->
                <button 
                    @click.prevent="resendCode"
                    :disabled="loading"
                    class="w-full py-2 border border-gray-300 text-gray-700 rounded-lg 
                        hover:bg-gray-100 focus:outline-none flex items-center justify-center"
                >
                    <span v-if="!loading">Resend the code</span>
                    <span v-if="loading">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Sending...
                    </span>
                </button>

            </form>
        </div>
    </div>
</template>

<script>


import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2

export default {
    name: "OTPError",
    data() {
        return {
            otp: ["", "", "", "", "", ""], // 6-digit OTP
            errorMessage: "", // Simulated error state
            loading: false, 
            isOtpValid: null,
        };
    },
    computed: {
        errorBorder() {
            return this.errorMessage ? "border-red-500" : "border-gray-300";
        },
        otpBorderClass() {
            if (this.isOtpValid === true) return "border-green-500 focus:ring-green-300"; // ✅ Success
            if (this.isOtpValid === false) return "border-red-500 focus:ring-red-300"; // ❌ Error
            return "border-gray-300 focus:ring-blue-300"; // Default
        }
    },
    methods: {
        async validateOTP() {
            if (this.otp.join('').length === 6) {
                try {
                    const token = localStorage.getItem('token');
                    const response = await axios.post('/api/otp/store', {
                        otp: this.otp.join('')
                    }, {
                        headers: { Authorization: `Bearer ${token}` }
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Verification Successful',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    this.isOtpValid = true; // ✅ Success border

                    // Redirect user to dashboard
                    setTimeout(() => {
                        this.$router.push('/dashboard');
                    }, 1500);

                } catch (error) {
                    this.errorMessage = error.response?.data?.message || "Invalid OTP. Please try again.";

                    Swal.fire({
                        icon: 'error',
                        title: 'Verification Failed',
                        text: this.errorMessage,
                    });

                    this.isOtpValid = false; // ❌ Error border

                    // Clear input fields on failure
                    setTimeout(() => {
                        this.otp = ["", "", "", "", "", ""];
                        this.$refs.otpInput[0].focus();
                        this.isOtpValid = null; // Reset border color
                    }, 1500);
                }
            }
        },
        async resendCode() {

            this.loading = true;

            try {
                const token = localStorage.getItem('token'); // Ensure user is authenticated
                const response = await axios.get('/api/otp/send', {
                    headers: { Authorization: `Bearer ${token}` }
                });

                Swal.fire({
                    icon: 'success',
                    title: 'OTP has been successfully sent',
                    text: response.data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                this.errorMessage = "Failed to send OTP. Please try again.";
                Swal.fire({
                    icon: 'error',
                    title: 'OTP Failed',
                    text: 'Failed to send OTP. Please try again.',
                });
            } finally {
                this.loading = false; // ✅ Stop loading
            }
        },
        // focusNext(index) {
        //     if (this.otp[index].length === 1 && index < 5) {
        //         this.$refs[`otp${index + 1}`][0].focus();
        //     }
        // },
        focusNext(index) {
            if (this.otp[index].length === 1 && index < 5) {
                this.$refs[`otp${index + 1}`][0].focus();
            }

            // Auto-submit when last digit is entered
            if (this.otp.join('').length === 6) {
                this.validateOTP();
            }
        },
        // resendCode() {
        //     this.errorMessage = ""; // Clear error on resending
        //     alert("A new verification code has been sent!");
        // },
        goBack() {
            this.$router.push("/login");
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
    },
};
</script>

<style scoped>
.otp-input {
    @apply w-10 h-12 text-center text-lg font-bold border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300;
}
</style>
