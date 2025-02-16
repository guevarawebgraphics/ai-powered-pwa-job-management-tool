<template>
    <div>
        <div class="w-full max-w-md p-8 space-y-6">

            <!-- Back Button -->
            <button @click="goBack" class="absolute left-4 top-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-lg"></i>
            </button>

            <img src="../../../public/images/logo.png" alt="Company Logo" class="w-32 mx-auto" />

            <div v-if="!isVerified">
                <h1 class="text-4xl leading-[48px] font-bold text-[#232850] text-center">
                    Verify your email
                </h1>

                <p class="text-sm leading-[22px] font-normal text-[#9095A0] text-center">
                    Please enter the verification code <b>we sent to your email address</b> to complete the verification
                    process.
                </p>
            </div>

            <!-- OTP Input -->
            <div class="flex justify-center space-x-2" v-if="!isVerified">
                <input type="text" v-model="otp[0]" maxlength="1" class="otp-input" :class="otpBorderClass" ref="otp0"
                    @input="focusNext(0)">
                <input type="text" v-model="otp[1]" maxlength="1" class="otp-input" :class="otpBorderClass" ref="otp1"
                    @input="focusNext(1)">
                <input type="text" v-model="otp[2]" maxlength="1" class="otp-input" :class="otpBorderClass" ref="otp2"
                    @input="focusNext(2)">
                <input type="text" v-model="otp[3]" maxlength="1" class="otp-input" :class="otpBorderClass" ref="otp3"
                    @input="focusNext(3)">
                <input type="text" v-model="otp[4]" maxlength="1" class="otp-input" :class="otpBorderClass" ref="otp4"
                    @input="focusNext(4)">
                <input type="text" v-model="otp[5]" maxlength="1" class="otp-input" :class="otpBorderClass" ref="otp5"
                    @input="focusNext(5)">
            </div>

            <!-- Error Message -->
            <p v-if="errorMessage" class="text-sm text-red-500 text-center flex items-center justify-center">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ errorMessage }}
            </p>

            <!-- Verify Button -->
            <button
                class="w-full py-2 border-0 rounded-lg flex items-center justify-center text-[#117B34FF] bg-green-300 cursor-not-allowed font-normal focus:outline-none focus:ring-0">
                <i class="fas fa-spinner fa-spin text-lg mr-2 text-[#117B34FF]"></i>
                Verify
            </button>


        </div>
    </div>
</template>

<script>
export default {
    name: "OTPSuccess",
    data() {
        return {
            otp: ["", "", "", "", "", ""], // 6-digit OTP
            errorMessage: "", // Error state
            isVerifying: false, // Loader state
            isVerified: false // Success state
        };
    },
    computed: {
        otpBorderClass() {
            // if (this.isVerified) return "border-green-500";
            // if (this.errorMessage) return "border-red-500";
            // return "border-gray-300";
            return "border-green-500";
        }
    },
    methods: {
        focusNext(index) {
            if (this.otp[index].length === 1 && index < 5) {
                this.$refs[`otp${index + 1}`][0].focus();
            }
        },
        verifyOTP() {
            this.isVerifying = true;
            this.errorMessage = "";

            // Simulate API request
            setTimeout(() => {
                const enteredOTP = this.otp.join("");
                if (enteredOTP === "537689") { // Example success OTP
                    this.isVerified = true;
                } else {
                    this.errorMessage = "Wrong verify code";
                }
                this.isVerifying = false;
            }, 2000);
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
