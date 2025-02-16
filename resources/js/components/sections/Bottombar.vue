<template>
    <nav class="fixed bottom-0 left-0 w-full bg-white shadow-md border-t flex justify-around py-2 z-50">
        <button v-for="item in navItems" :key="item.name" @click="navigateTo(item)"
            class="flex flex-col items-center text-[#565E6CFF] hover:text-[#565E6CFF]-800 transition duration-300"
            :class="{ 'text-[#000000FF] font-bold': activeItem === item.name }">
            <i :class="item.icon" class="text-2xl"></i>
            <span class="text-sm mt-1">{{ item.label }}</span>
        </button>
    </nav>
</template>

<script>
export default {
    name: "Bottombar",
    data() {
        return {
            activeItem: "Calendar", // Default active item
            navItems: [
                { name: "Home", label: "Home", icon: "fas fa-home", route: "/dashboard" },
                { name: "Calendar", label: "Calendar", icon: "fas fa-calendar-alt", route: "/schedules" },
                { name: "CoPilot", label: "CoPilot", icon: "fas fa-headset", route: "/copilot" },
                { name: "Analytics", label: "Analytics", icon: "fas fa-chart-bar", route: "/analytics" },
                { name: "Guild", label: "Guild", icon: "fas fa-project-diagram", route: "/guild-profile" }
            ],
        };
    },
    methods: {
        navigateTo(item) {
            this.activeItem = item.name;
            this.$router.push(item.route);
        },
    },
};
</script>

<style scoped>
/* Optional: Ensures bottom nav does not overlap content */
.bottom-spacing {
    padding-bottom: 4rem;
}
</style>
