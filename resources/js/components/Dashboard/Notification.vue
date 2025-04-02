<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Notification Tabs -->
        <div class="max-w-lg mx-auto p-6">
            <div class="flex space-x-2 mb-4">
                <button
                    v-for="(tab, index) in tabs"
                    :key="index"
                    @click="changeTab(tab)"
                    class="px-4 py-2 rounded-full font-semibold"
                    :class="{
                        'bg-[#232850] text-white': activeTab === tab,
                        'bg-gray-100 text-gray-700': activeTab !== tab,
                    }"
                >
                    {{ tab }}
                </button>
            </div>

            <!-- Notification Section -->
            <div v-if="notificationData.length > 0">
                <div
                    v-for="(section, secIndex) in notificationData"
                    :key="secIndex"
                >
                    <h2 class="text-xs font-bold text-gray-500 uppercase mb-2">
                        {{ section.date }}
                    </h2>
                    <div
                        v-for="(notification, index) in section.items"
                        :key="index"
                        @click="markAsRead(notification)"
                        class="bg-white rounded-lg shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex items-center space-x-4 mb-3 cursor-pointer"
                    >
                        <!-- Icon -->
                        <i
                            :class="notification.icon"
                            class="text-2xl text-[#9095A0FF]"
                        ></i>

                        <!-- Notification Content -->
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-[#171A1FFF]">
                                {{ notification.title }}
                            </h3>
                            <p class="text-sm text-[#323842FF]">
                                {{ notification.description }}
                            </p>
                            <span class="text-xs text-[#323842FF]">{{
                                notification.time
                            }}</span>
                        </div>

                        <!-- Unread Indicator -->
                        <div
                            v-if="notification.unread"
                            class="w-2 h-2 bg-black rounded-full"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- No Record Found -->
            <div v-else class="text-center text-gray-500 mt-6">
                <p>No notifications found.</p>
            </div>
        </div>

        <BottomNav />
    </div>
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import axios from "axios"; // Ensure axios is installed
// import '/resources/js/echo.js';
import { messaging } from "../../firebase";
import { mapState } from "vuex";

export default {
    components: { NavBar, BottomNav },
    name: "NotificationPage",
    data() {
        return {
            activeTab: "Gigs", // Default active tab
            tabs: ["Gigs", "Guild", "Level Up"],
            notifications: [], // Initially empty, will be populated with API data
            loading: false, // For future loading state (if needed),
            user_data: {},
            unseenCount: 0,
        };
    },
    computed: {
        ...mapState(["unseenNotification", "notificationData"]),
        notifyType() {
            return this.activeTab === "Gigs"
                ? 1
                : this.activeTab === "Guild"
                ? 2
                : 3;
        },
    },
    methods: {
        async userData() {
            const token = localStorage.getItem("token");
            const user_query = await axios.get("/api/user", {
                headers: { Authorization: `Bearer ${token}` },
            });

            const user = user_query.data.user;
            this.user_data = user;

            // this.fetchNotifications(); // Remove due to vuex centralized variable for notification listings
        },
        async fetchNotifications() {
            try {
                this.loading = true; // Set loading state (if needed)
                const userId = this.user_data.id; // Replace with dynamic user ID if needed
                const response = await axios.get(
                    `/api/notify/${userId}/${this.notifyType}`
                );

                if (response.data && response.data.data.length > 0) {
                    this.notifications = this.formatNotifications(
                        response.data.data
                    );
                } else {
                    this.notifications = []; // No notifications found
                }
            } catch (error) {
                console.error("❌ Failed to fetch notifications:", error);
                this.notifications = []; // Clear notifications on error
            } finally {
                this.loading = false;
            }

            this.$store.commit("setNotificationData", this.notifications);
        },
        formatNotifications(data) {
            // Group notifications by date
            const grouped = {};
            data.forEach((item) => {
                const date = new Date(item.created_at).toLocaleDateString(
                    "en-US",
                    {
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                    }
                );

                if (!grouped[date]) {
                    grouped[date] = { date, items: [] };
                }

                grouped[date].items.push({
                    id: item.id,
                    icon: item.icon_type || "fas fa-bell", // Default icon
                    title: item.name,
                    description: item.content,
                    time: this.formatTimeAgo(item.created_at),
                    unread: item.is_seen == 1 ? false : true,
                });
            });

            // Convert grouped object to array
            return Object.values(grouped);
        },
        formatTimeAgo(timestamp) {
            const diff = Math.floor((new Date() - new Date(timestamp)) / 1000);
            if (diff < 60) return `${diff}s ago`;
            if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
            if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
            return `${Math.floor(diff / 86400)}d ago`;
        },
        changeTab(tab) {
            this.activeTab = tab;
            this.fetchNotifications(); // Fetch new notifications when tab changes
        },

        async fetchUnseenCount() {
            try {
                const userId = this.user_data.id; // Replace with dynamic user ID if needed
                console.log(`user_id_to_view ${userId}`);
                const response = await axios.get(
                    `/api/notify/get/${userId}/unseen`
                );

                if (response.data && response.data.count !== undefined) {
                    this.unseenCount = response.data.count;
                }

                this.$store.commit("setUnseenNotification", this.unseenCount);

                console.log(`New Unseen: `, this.unseenCount);
            } catch (error) {
                console.error(
                    "❌ Failed to fetch unseen notification count:",
                    error
                );
            }
        },

        async markAsRead(notification) {
            if (!notification.unread) return; // If already read, do nothing

            try {
                const response = await axios.post(
                    `/api/notify/update/${notification.id}`,
                    {
                        is_seen: 1,
                    }
                );

                // Update UI instantly
                notification.unread = false;

                this.fetchNotifications();

                console.log(`Notification: `, response);
            } catch (error) {
                console.error("❌ Failed to mark notification as read:", error);
            }

            this.fetchUnseenCount();
        },
    },
    mounted() {
        this.userData();
        // window.Echo.channel('notifications') // Public Channel (No Auth)
        //     .listen('NewNotificationEvent', (event) => {
        //         this.fetchNotifications();
        //     });
    },
};
</script>
