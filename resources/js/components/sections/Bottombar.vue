<template>
    <!-- <nav class="fixed bottom-0 left-0 w-full bg-white shadow-md border-t flex justify-around py-2 z-50"> -->
    <nav
        class="fixed bottom-0 left-0 w-full bg-white shadow-md border-t flex justify-around py-2 z-[9999] pointer-events-auto">

        <button v-for="item in navItems" :key="item.name" @click="navigateTo(item)"
            class="flex flex-col items-center hover:text-[#171A1FFF] transition duration-300"
            :class="isActive(item) ? 'text-[#171A1FFF] font-bold' : 'text-[#565E6CFF]'">
            <i :class="[item.icon, 'text-2xl']"></i>
            <span class="text-sm mt-1">{{ item.label }}</span>
        </button>
    </nav>
</template>


<script>
import { bus } from './DAX.vue'; 

export default {
    name: "Bottombar",
    computed: {
        currentRoute() {
            return this.$route.path;
        }
    },
    data() {
        return {
            navItems: [
                { name: "Home", label: "Home", icon: "fas fa-home", route: "/dashboard" },
                { name: "Calendar", label: "Calendar", icon: "fas fa-calendar-alt", route: "/schedules" },
                { name: "DAX", label: "DAX", icon: "fas fa-headset", route: "/dax" },
                { name: "Analytics", label: "Analytics", icon: "fas fa-chart-bar", route: "/analytics" },
                { name: "Guild", label: "Guild", icon: "fas fa-project-diagram", route: "/guild-profile" }
            ],
        };
    },
    methods: {
        navigateTo(item) {
            if (item.name === "DAX") {
                bus.emit("open-dax"); // ✅ Triggers the modal
            } else {
                this.$router.push(item.route);
            }
        },
        isActive(item) {
            return this.currentRoute === item.route;
        }
    }
};
</script>

<style scoped>
/* Optional: Ensures bottom nav does not overlap content */
.bottom-spacing {
    padding-bottom: 4rem;
}
</style>
