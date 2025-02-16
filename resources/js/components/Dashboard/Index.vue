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
                    <h2 class="text-lg text-[#171A1FFF]">Full Name</h2>
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
                <h2 class="text-xl text-[#171A1FFF]">June, 12</h2>
                <span class="text-gray-500 text-sm">Last 28 days</span>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col items-start">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-headphones-simple text-lg text-[#171A1FFF]"></i>
                        <span class="text-xl font-medium text-[#666666FF]">DAX</span>
                    </div>
                    <!-- <p class="text-sm text-gray-500">Day gig streak</p> -->
                </div>

                <div
                    class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col items-start">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar-alt text-lg text-[#232850FF]"></i>
                        <span class="text-xl font-bold text-[#171A1FFF]">3</span>
                    </div>
                    <p class="text-sm text-[#666666FF]">Jobs Booked Today</p>
                </div>

                <div
                    class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col items-start">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-thumbs-up text-lg text-[#171A1FFF]"></i>
                        <span class="text-sm font-medium text-[#666666FF]">New Job Request Dryer, no Heat, Stuart</span>
                    </div>
                    <!-- <p class="text-sm text-gray-500">New Job Request Dryer, no Heat, Stuart</p> -->
                </div>

                <div
                    class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col items-start">
                    <p class="text-sm text-[#666666FF]">Earnings</p>
                    <div class="flex items-center space-x-2">
                        <span class="text-xl font-bold text-[#171A1FFF]">$2150</span>
                        <i class="fas fa-arrow-up text-green-500 text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Updates Section -->
        <div class="max-w-lg mx-auto p-6">
            <h2 class="text-xl text-[#171A1FFF]">Latest Updates</h2>

            <div class="space-y-4 mt-3">
                <div v-for="(update, index) in latestUpdates" :key="index"
                    class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col space-y-2">
                    <div class="flex items-start space-x-3">
                        <!-- Image/Icon -->
                        <img v-if="update.image" :src="update.image" class="w-12 h-12 rounded-md" />
                        <i v-else :class="update.icon" class="text-3xl text-gray-700"></i>

                        <!-- Content -->
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-[#222222FF]">{{ update.title }}</h3>
                            <p class="text-sm text-[#666666FF]">{{ update.description }}</p>
                        </div>
                    </div>

                    <hr class="border-gray-300 my-2" />

                    <!-- Bottom Section: Icons on Left, Arrow Down on Right -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4 items-center">
                            <span class="text-green-500 font-bold text-lg flex items-center">
                                <i class="fas fa-dollar-sign mr-1"></i> {{ update.amount }}
                            </span>
                            <i class="fas fa-thumbs-up text-xl text-[#171A1FFF]"></i>
                            <i class="fas fa-play-circle text-xl text-[#171A1FFF]"></i>
                            <i class="fas fa-info-circle text-xl text-[#171A1FFF]"></i>
                        </div>
                        <!-- Toggle Arrow -->
                        <i @click="toggleExpand(index)"
                            class="fas fa-chevron-down text-xl text-gray-500 cursor-pointer transition-transform duration-300"
                            :class="{ 'rotate-180': expandedIndex === index }"></i>
                    </div>

                    <!-- Expanded Content -->
                    <div v-if="expandedIndex === index" class="mt-2 p-2 bg-gray-100 rounded-md">
                        <p class="text-sm text-gray-700">
                            This is additional information about "{{ update.title }}". You can add more details here.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-lg mx-auto p-6 text-center">
            <a>
                <i
                    class="fas fa-chevron-down text-xl text-gray-500 cursor-pointer transition-transform duration-300"></i>
            </a>
        </div>

        <!-- Latest Updates Section -->
        <div class="max-w-lg mx-auto p-6">
            <h2 class="text-xl font-medium text-[#171A1FFF]">Earn While you Learn</h2>

            <div class="space-y-4 mt-3">
                <div v-for="(update, index) in earnWhileYouLearn" :key="index"
                    class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col space-y-2">
                    <div class="flex items-start space-x-3">
                        <!-- Image/Icon -->
                        <img v-if="update.image" :src="update.image" class="w-12 h-12 rounded-md" />
                        <i v-else :class="update.icon" class="text-3xl text-gray-700"></i>

                        <!-- Content -->
                        <div class="flex-1">
                            <p class="text-lg font-normal text-gray-800">{{ update.title }}</p>
                            <p class="text-sm text-gray-500">{{ update.description }}</p>
                        </div>
                    </div>

                    <hr class="border-gray-300 my-2" />

                    <!-- Bottom Section: Icons on Left, Arrow Down on Right -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4 items-center">
                            <span class="text-green-500 font-bold text-lg flex items-center">
                                <i class="fas fa-dollar-sign mr-1"></i> {{ update.amount }}
                            </span>
                            <i class="fas fa-thumbs-up text-xl text-[#171A1FFF]"></i>
                            <i class="fas fa-play-circle text-xl text-[#171A1FFF]"></i>
                            <i class="fas fa-info-circle text-xl text-[#171A1FFF]"></i>
                        </div>
                        
                        <!-- Toggle Arrow -->
                        <i @click="toggleExpandV2(index)"
                            class="fas fa-chevron-down text-xl text-gray-500 cursor-pointer transition-transform duration-300"
                            :class="{ 'rotate-180': expandedIndexV2 === index }"></i>
                    </div>

                    <!-- Expanded Content -->
                    <div v-if="expandedIndexV2 === index" class="mt-2 p-2 bg-gray-100 rounded-md">
                        <p class="text-sm text-gray-700">
                            This is additional information about "{{ update.title }}". You can add more details
                            here.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-lg mx-auto p-6 text-center">
            <a>
                <i
                    class="fas fa-chevron-down text-xl text-gray-500 cursor-pointer transition-transform duration-300"></i>
            </a>
        </div>

        <BottomNav />
    </div>
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";

export default {
    components: { NavBar, BottomNav },
    name: "DashboardIndex",
    data() {
        return {
            expandedIndex: null, // Stores the index of the expanded update
            expandedIndexV2: null,
            latestUpdates: [
                {
                    image: "../../../../images/washing-machine.png",
                    title: "Next Job in 3 hours.",
                    description: "Be prepared and Kick Ass. Watch suggested Repair videos, wash your butt and put on the uniform..",
                    amount: "100",
                },
                {
                    icon: "fas fa-award",
                    title: "Congratulations !!",
                    description: "Master of Electric Dryer No Heat Master of Electric Dryer No Heat",
                    amount: "50",
                },
                {
                    image: "../../../../images/washing-machine.png",
                    title: "Next Job in 3 hours.",
                    description: "Be prepared and Kick Ass. Watch suggested Repair videos, wash your butt and put on the uniform..",
                    amount: "300",
                },
            ],
            earnWhileYouLearn: [
                {
                    image: "../../../../images/earn-while-you-learn.png",
                    title: "Dryer Repair: Squeaking, Squealing, Thu..",
                    description: "First 75 days 1 hours",
                    amount: "100",
                },
                {
                    image: "../../../../images/earn-while-you-learn.png",
                    title: "Dryer Repair: Squeaking, Squealing, Thu..",
                    description: "First 75 days 1 hours",
                    amount: "300",
                },
            ]
        };
    },
    methods: {
        toggleExpand(index) {
            // If the same index is clicked, collapse it, otherwise expand
            this.expandedIndex = this.expandedIndex === index ? null : index;
        },
        toggleExpandV2(index) {
            // If the same index is clicked, collapse it, otherwise expand
            this.expandedIndexV2 = this.expandedIndexV2 === index ? null : index;
        }
    }
};
</script>
