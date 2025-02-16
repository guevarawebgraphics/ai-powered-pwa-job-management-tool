<template>
    <div>
        <div class="w-full max-w-md p-8 space-y-6">

            <!-- Back Button -->
            <button @click="goBack" class="absolute left-4 top-4 text-gray-600 hover:text-gray-800">
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
                    <input type="text" v-model="otp[0]" maxlength="1" class="otp-input" :class="errorBorder" ref="otp0"
                        @input="focusNext(0)">
                    <input type="text" v-model="otp[1]" maxlength="1" class="otp-input" :class="errorBorder" ref="otp1"
                        @input="focusNext(1)">
                    <input type="text" v-model="otp[2]" maxlength="1" class="otp-input" :class="errorBorder" ref="otp2"
                        @input="focusNext(2)">
                    <input type="text" v-model="otp[3]" maxlength="1" class="otp-input" :class="errorBorder" ref="otp3"
                        @input="focusNext(3)">
                    <input type="text" v-model="otp[4]" maxlength="1" class="otp-input" :class="errorBorder" ref="otp4"
                        @input="focusNext(4)">
                    <input type="text" v-model="otp[5]" maxlength="1" class="otp-input" :class="errorBorder" ref="otp5"
                        @input="focusNext(5)">
                </div>

                <!-- Error Message -->
                <p v-if="errorMessage" class="text-sm text-red-500 text-center flex items-center justify-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ errorMessage }}
                </p>

                <!-- Resend Code Button -->
                <button @click.prevent="resendCode"
                    class="w-full py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-none">
                    Resend the code
                </button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "OTPError",
    data() {
        return {
            otp: ["", "", "", "", "", ""], // 6-digit OTP
            errorMessage: "Wrong verify code", // Simulated error state
        };
    },
    computed: {
        errorBorder() {
            return this.errorMessage ? "border-red-500" : "border-gray-300";
        }
    },
    methods: {
        focusNext(index) {
            if (this.otp[index].length === 1 && index < 5) {
                this.$refs[`otp${index + 1}`][0].focus();
            }
        },
        resendCode() {
            this.errorMessage = ""; // Clear error on resending
            alert("A new verification code has been sent!");
        },
        goBack() {
            this.$router.push("/login");
        },
    },
};
</script>

<style scoped>
.otp-input {
    @apply w-10 h-12 text-center text-lg font-bold border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300;
}
</style>
