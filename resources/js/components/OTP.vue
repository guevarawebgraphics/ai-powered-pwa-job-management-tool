<template>
    <div class="w-full max-w-md p-8 space-y-6">
        <img src="../../../public/images/logo.png" alt="Company Logo" class="w-32 mx-auto" />

        <h1 class="text-4xl leading-[48px] font-bold text-[#232850] text-center">
            Verify your email
        </h1>

        <p class="text-sm leading-[22px] font-normal text-[#9095A0] text-center">
            Please enter the verification code <b>we sent to your email address</b>.
        </p>

        <form @submit.prevent="validateOTP" class="space-y-6">
            <!-- OTP Input -->
            <div class="flex justify-center space-x-2">
                <input v-for="(digit, index) in otp" :key="index" type="tel" inputmode="numeric" v-model="otp[index]"
                    maxlength="1" class="otp-input" ref="otpInput" :class="otpBorderClass"
                    @input="focusNext(index, $event)" @keydown.backspace="focusPrevious(index, $event)" />
            </div>

            <!-- Resend Code Button -->
            <button @click.prevent="resendCode" :disabled="loading" class="w-full py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 
                    focus:outline-none flex items-center justify-center">
                <span v-if="!loading">Resend the code</span>
                <span v-if="loading">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Sending...
                </span>
            </button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    name: "OTPVerification",
    data() {
        return {
            otp: ["", "", "", "", "", ""], // 6-digit OTP
            errorMessage: "",
            loading: false,
            isOtpValid: null,
        };
    },
    computed: {
        otpBorderClass() {
            if (this.isOtpValid === true) return "border-green-500 focus:ring-green-300";
            if (this.isOtpValid === false) return "border-red-500 focus:ring-red-300";
            return "border-gray-300 focus:ring-blue-300";
        }
    },
    mounted() {
        // Check if token exists in URL
        const urlParams = new URLSearchParams(window.location.search);
        const tokenFromUrl = urlParams.get('token');

        if (tokenFromUrl) {
            localStorage.setItem('token', tokenFromUrl);
        }
    },
    methods: {
        focusNext(index, event) {
            if (event.target.value && index < 5) {
                this.$nextTick(() => {
                    this.$refs.otpInput[index + 1].focus();
                });
            }

            // Auto-submit when last digit is entered
            if (this.otp.join('').length === 6) {
                this.validateOTP();
            }
        },
        focusPrevious(index, event) {
            if (!this.otp[index] && index > 0) {
                this.$nextTick(() => {
                    this.$refs.otpInput[index - 1].focus();
                });
            }
        },
        async validateOTP() {
            if (this.otp.join('').length === 6) {
                try {
                    const token = localStorage.getItem('token');
                    const response = await axios.post('/api/otp/store', { otp: this.otp.join('') }, {
                        headers: { Authorization: `Bearer ${token}` }
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Verification Successful',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    this.isOtpValid = true;
                    setTimeout(() => this.$router.push('/dashboard'), 1500);

                } catch (error) {
                    this.errorMessage = error.response?.data?.message || "Invalid OTP. Please try again.";
                    Swal.fire({ icon: 'error', title: 'Verification Failed', text: this.errorMessage });

                    this.isOtpValid = false;
                    setTimeout(() => {
                        this.otp = ["", "", "", "", "", ""];
                        this.$refs.otpInput[0].focus();
                        this.isOtpValid = null;
                    }, 1500);
                }
            }
        },
        async resendCode() {
            this.loading = true;
            try {
                const token = localStorage.getItem('token');
                const response = await axios.get('/api/otp/send', { headers: { Authorization: `Bearer ${token}` } });

                Swal.fire({
                    icon: 'success',
                    title: 'OTP Sent',
                    text: response.data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'OTP Failed', text: 'Failed to send OTP. Please try again.' });
            } finally {
                this.loading = false;
            }
        }
    },
};
</script>

<style scoped>
.otp-input {
    @apply w-12 h-12 text-center text-lg font-bold border rounded-lg focus:outline-none focus:ring-2;
}
</style>
