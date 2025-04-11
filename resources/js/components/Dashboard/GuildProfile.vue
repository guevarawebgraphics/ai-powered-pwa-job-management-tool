<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Guild Profile -->
        <div class="flex flex-col items-center p-6">
            <!-- Profile Picture -->
            <div class="relative w-24 h-24">
                <!-- Profile Picture -->
                <img :src="previewPhoto" alt="Profile Picture"
                    class="w-24 h-24 rounded-md border border-gray-300 shadow-md object-cover" />

                <!-- File Input (Hidden) -->
                <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="uploadProfilePhoto" />
            </div>

            <!-- Name & Title -->
            <div class="mt-3 text-center">
                <div class="relative inline-block">
                    <h2 class="text-xl text-[#171A1FFF]">{{ name ?? "--" }}</h2>
                </div>
                <p class="text-[#9095A0FF]">{{ professionalTitle ?? "--" }}</p>
            </div>

            <Ratings />
        </div>


        <!-- Statistics Section -->
        <div class="max-w-lg mx-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-[#232850FF]">June, 12</h2>
                <span class="text-gray-500 text-sm">Last 28 days</span>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-fire text-lg text-gray-700"></i>
                        <span class="text-xl font-bold">3</span>
                    </div>
                    <p class="text-sm text-gray-500">Day gig streak</p>
                </div>

                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar-alt text-lg text-gray-700"></i>
                        <span class="text-xl font-bold">117</span>
                    </div>
                    <p class="text-sm text-gray-500">Days as technician</p>
                </div>

                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-thumbs-up text-lg text-gray-700"></i>
                        <span class="text-xl font-bold">27</span>
                    </div>
                    <p class="text-sm text-gray-500">5-star reviews</p>
                </div>

                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <p class="text-sm text-gray-500">Earnings</p>
                    <div class="flex items-center space-x-2">
                        <span class="text-xl font-bold">$2150</span>
                        <i class="fas fa-arrow-up text-green-500 text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Badges Section -->
        <div class="max-w-lg mx-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-[#232850FF]">Badges</h2>
                <a href="#" class="text-blue-500 text-sm">View All</a>
            </div>

            <div class="bg-white rounded-lg shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 space-y-4">
                <div v-for="(badge, index) in badges" :key="index" class="space-y-2">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <i :class="badge.icon" class="text-2xl text-gray-500"></i>
                            <h3 class="text-lg font-semibold text-gray-800">{{ badge.name }}</h3>
                        </div>
                        <span class="text-sm text-gray-600">{{ badge.progress }}</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-300 h-2 rounded-full">
                        <div class="h-2 rounded-full bg-red-500" :style="{ width: badge.percentage + '%' }"></div>
                    </div>

                    <p class="text-sm text-gray-500">{{ badge.description }}</p>
                </div>
            </div>
        </div>

        <!-- Guild Posts Section -->
        <div class="max-w-lg mx-auto p-6">
            <h2 class="text-xl font-bold text-[#232850FF] text-center">Guild Posts</h2>
            <p class="text-gray-500 text-normal text-center mb-4">
                Your highest rated posts in The Guild Forum
            </p>

            <div
                class="bg-white rounded-lg shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex items-center space-x-4">
                <!-- Upvote Count -->
                <div class="flex flex-col items-center">
                    <i class="fas fa-angle-up text-2xl text-[#8C8C8CFF]"></i>
                    <span class="text-xl font-bold text-[#8C8C8CFF]">714</span>
                </div>

                <!-- Post Details -->
                <div class="flex-1">
                    <p class="text-sm font-normal text-[#222222FF]">
                        Samsung WMD79899EV Disassembly Question
                    </p>
                    <p class="text-sm text-gray-500">Posted 2 Months Ago</p>
                    <a href="#" class="text-blue-500 text-sm font-semibold">Read More</a>
                </div>
            </div>
        </div>

        <BottomNav />
    </div>
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import Ratings from "../sections/Ratings.vue";
import axios from "axios"; // Ensure axios is imported

export default {
    components: { NavBar, BottomNav, Ratings },
    name: "GuildProfilePage",
    data() {
        return {
            name: "--",
            professionalTitle: "--",
            previewPhoto: "/images/avatar.png",
            badges: [
                {
                    name: "Rockstar",
                    icon: "fas fa-star",
                    progress: "433/500",
                    percentage: 86,
                    description: "Completed 500 jobs",
                },
                {
                    name: "Steady Burner",
                    icon: "fas fa-fire",
                    progress: "3/5",
                    percentage: 60,
                    description: "Reach a 5 day gig streak",
                },
                {
                    name: "Machine",
                    icon: "fas fa-robot",
                    progress: "0/2",
                    percentage: 10,
                    description: "Complete at least 2 gigs on Saturday or Sunday",
                },
            ],
        };
    },
    created() {
        this.fetchUserData();
    },
    methods: {
        async fetchUserData() {
            const token = localStorage.getItem("token");
            if (!token) {
                console.error("No token found in localStorage");
                return;
            }
            try {
                const response = await axios.get("/api/user", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    },
                });

                const userData = response.data.user;

                // Check if profile_photo exists and is valid
                if (
                    userData.profile_photo &&
                    userData.profile_photo !== "null"
                ) {
                    this.previewPhoto = userData.profile_photo.startsWith(
                        "http"
                    )
                        ? userData.profile_photo
                        : `${process.env.VUE_APP_BASE_URL}/storage/${userData.profile_photo}`;
                } else {
                    this.previewPhoto = "/images/avatar.png";
                }

                // Set user info
                this.name = userData.name;
                this.professionalTitle = userData.professional_title;
            } catch (error) {
                console.error("Error fetching user data:", error);
            }
        },
    }
};
</script>
