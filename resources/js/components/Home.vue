<template>
    <div>
        <div class="w-full max-w-md space-y-6">
            <img src="../../../public/images/logo.png" alt="Company Logo" class="w-32 mx-auto" />

            <div>
                <h1 class="text-4xl leading-[48px] text-[#232850] text-center">
                    Nice to see you!
                </h1>

                <p @click="redirectToRegister"
                    class="text-sm leading-[22px] font-normal text-[#9095A0] text-center cursor-pointer">
                    Create your account
                </p>
            </div>

            <form action="#" method="POST" class="space-y-6">
                <button type="button" @click="redirectToLogin"
                    class="w-full px-4 py-2 font-semibold text-white bg-[#232850] rounded-xl hover:bg-[#1d2142] focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <i class="fas fa-envelope text-[#fff]"></i> Continue with Email
                </button>
            </form>
        </div>
    </div>
</template>

<script>

import axios from "axios"; // Ensure axios is imported

export default {
    name: "Home",
    // created() {
    //     this.checkAuthAndRedirect();
    // },

    methods: {
        redirectToRegister() {
            this.$router.push("/register");
        },
        redirectToLogin() {
            this.$router.push("/login");
        },
        // async checkAuthAndRedirect() {
        //     const token = localStorage.getItem('token');
        //     if (token) {
        //         try {
        //             const res = await axios.get('/api/user', {
        //                 headers: { Authorization: `Bearer ${token}` }
        //             });
        //             const user = res.data.user;

        //             if (user) {
        //                 this.$router.replace("/dashboard");
        //             }

        //             console.log(`checkAuthAndRedirect: `, user);
        //         } catch (error) {

        //             console.log(`checkAuthAndRedirect: `, error);
        //             this.$router.replace("/");
        //         }
        //     } else {

        //         this.$router.replace("/");
        //     }
        // }
    }
};
</script>
