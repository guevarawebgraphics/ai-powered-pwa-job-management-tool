<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Notification Tabs -->
        <div class="max-w-lg mx-auto p-6">
            <div class="flex space-x-2 mb-4">
                <button v-for="(tab, index) in tabs" :key="index" @click="activeTab = tab"
                    class="px-4 py-2 rounded-full font-semibold" :class="{
                        'bg-[#232850] text-white': activeTab === tab,
                        'bg-gray-100 text-gray-700': activeTab !== tab
                    }">
                    {{ tab }}
                </button>
            </div>

            <!-- Notification Section -->
            <div v-for="(section, secIndex) in notifications" :key="secIndex">
                <h2 class="text-xs font-bold text-gray-500 uppercase mb-2">{{ section.date }}</h2>
                <div v-for="(notification, index) in section.items" :key="index"
                    class="bg-white rounded-lg shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex items-center space-x-4 mb-3">

                    <!-- Icon -->
                    <i :class="notification.icon" class="text-2xl text-[#9095A0FF]"></i>

                    <!-- Notification Content -->
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-gray-800">{{ notification.title }}</h3>
                        <p class="text-sm text-gray-500">{{ notification.description }}</p>
                        <span class="text-xs text-gray-400">{{ notification.time }}</span>
                    </div>

                    <!-- Unread Indicator -->
                    <div v-if="notification.unread" class="w-2 h-2 bg-black rounded-full"></div>
                </div>
            </div>
        </div>

        <BottomNav />
    </div>
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";

export default {
    components: { NavBar, BottomNav },
    name: "NotificationPage",
    data() {
        return {
            activeTab: "Gigs",
            tabs: ["Gigs", "Guild", "Level Up"],
            notifications: [
                {
                    date: "Today",
                    items: [
                        { icon: "fas fa-tshirt", title: "Notification Title", description: "Laborum aliqua do mollit nostrud irure sit nulla duis nisi nulla est...", time: "1m", unread: true },
                        { icon: "fas fa-tools", title: "Notification Title", description: "Laborum aliqua do mollit nostrud irure sit nulla duis nisi nulla est...", time: "5m", unread: true },
                        { icon: "fas fa-camera", title: "Notification Title", description: "Laborum aliqua do mollit nostrud irure sit nulla duis nisi nulla est...", time: "24m", unread: false },
                        { icon: "fas fa-phone-alt", title: "Notification Title", description: "Laborum aliqua do mollit nostrud irure sit nulla duis nisi nulla est...", time: "2h", unread: false }
                    ]
                },
                {
                    date: "Yesterday",
                    items: [
                        { icon: "fas fa-clipboard", title: "Notification Title", description: "Laborum aliqua do mollit nostrud irure sit nulla duis nisi nulla est...", time: "17h", unread: false },
                        { icon: "fas fa-dollar-sign", title: "Notification Title", description: "Laborum aliqua do mollit nostrud irure sit nulla duis nisi nulla est...", time: "19h", unread: false }
                    ]
                }
            ]
        };
    }
};
</script>
