<template>
    <nav
        class="fixed bottom-0 left-0 w-full bg-white shadow-md border-t flex justify-around py-2 z-[9999] pointer-events-auto">
        <button v-for="item in navItems" :key="item.name" @click="navigateTo(item)"
            class="flex flex-col items-center transition duration-300" :class="[
                isActive(item) ? 'text-[#171A1FFF] font-bold' : 'text-[#565E6CFF]',
                item.name === 'DAX' && isDaxActive ? 'pulse-active text-red-500' : '',
                'hover:text-[#171A1FFF]'
            ]" :style="item.name === 'DAX' && isDaxActive ? {
                transform: `scale(${1 + volume * 1.5})`
            } : {}">
            <i :class="[item.icon, 'text-2xl']"></i>
            <span class="text-sm mt-1">{{ item.label }}</span>
        </button>
    </nav>
</template>

<script>
import { bus } from './DAX.vue';

export default {
    name: "Bottombar",
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
    computed: {
        currentRoute() {
            return this.$route.path;
        },
        isDaxActive() {
            return this.$store.state.isDaxActive;
        },
        volume() {
            return this.$store.state.daxVolume || 0;
        }
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
.bottom-spacing {
    padding-bottom: 4rem;
}

@keyframes pulse {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.1);
    }
}

.pulse-active {
    animation: pulse 1.5s infinite;
}
</style>
