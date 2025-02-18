<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Guild Profile -->
        <div class="flex flex-col items-center p-6">
            <!-- Profile Picture -->
            <div class="relative">
                <img src="../../../../public/images/profile.png" alt="Profile Picture"
                    class="w-24 h-24 rounded-md border border-gray-300 shadow-md" />
                <!-- Edit Icon (Top Right) -->
                <button class="absolute top-0 right-0 bg-white border rounded-full p-1 shadow-md">
                    <i class="fas fa-external-link-alt text-[#BCC1CAFF]"></i>
                </button>
            </div>

            <!-- Name & Title -->
            <div class="mt-3 text-center">
                <div class="relative inline-block">
                    <h2 class="text-lg font-bold text-[#232850FF]">Full Name</h2>
                    <!-- Edit Icon -->
                    <button class="absolute -right-6 top-1">
                        <i class="fas fa-edit text-[#BCC1CAFF]"></i>
                    </button>
                </div>
                <p class="text-[#9095A0FF]">Professional title</p>
            </div>

            <!-- Star Rating -->
            <div class="flex space-x-1 mt-3">
                <i class="fas fa-star text-4xl text-[#232850FF]"></i>
                <i class="fas fa-star text-4xl text-[#232850FF]"></i>
                <i class="fas fa-star text-4xl text-[#232850FF]"></i>
                <i class="fas fa-star text-4xl text-[#232850FF]"></i>
                <i class="fas fa-star text-4xl text-[#232850FF]"></i>
            </div>
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

export default {
    components: { NavBar, BottomNav },
    name: "GuildProfilePage",
    data() {
        return {
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
};
</script>
