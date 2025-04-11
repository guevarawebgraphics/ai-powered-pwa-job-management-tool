<template>
    <div>

        <PreLoader v-if="$store.state.isLoading" />
        <!-- The Layout -->
        <router-view></router-view>
        <!-- This will now load Home.vue, Login.vue, Register.vue -->
    </div>
</template>

<script>
import PreLoader from './sections/PreLoader.vue';
export default {
    name: "App",
    components: {
        PreLoader
    },
    mounted() {
        // Commit to set isLoading to true, causing the pre-loader to appear
        this.$store.commit("setLoading", true);

        // For testing, we can automatically hide it after 3 seconds
        setTimeout(() => {
            this.$store.commit("setLoading", false);
        }, 2000);
    }
};
</script>
