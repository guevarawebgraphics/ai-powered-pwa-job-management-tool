<template>
    <div>
        <div class="w-full max-w-md p-8 space-y-6">

            <!-- Back Button -->
            <button @click="goBack" class="absolute left-4 top-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-lg"></i>
            </button>

            <img src="../../../public/images/logo.png" alt="Company Logo" class="w-32 mx-auto" />

            <div >
                <h1 class="text-4xl leading-[48px] font-bold text-[#232850] text-center">
                    Hello Again!
                </h1>

                <p class="text-sm leading-[22px] font-normal text-[#9095A0] text-center">
                    Log into your account
                </p>
            </div>




        </div>
    </div>
</template>

<script>
export default {
    name: "Loading",
    data() {
        return {

        };
    },
    computed: {

    },
    methods: {

    },
};
</script>
