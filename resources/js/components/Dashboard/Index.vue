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
                    <h2 class="text-xl text-[#171A1FFF]">{{ name ?? '--' }}</h2>
                </div>
                <p class="text-[#9095A0FF]">{{ professionalTitle ?? '--' }}</p>
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
                <h2 class="text-xl text-[#171A1FFF]">{{ formattedDate }}</h2>
                <span class="text-gray-500 text-sm">Last 28 days</span>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3">
                <button type="button"
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-headphones-simple text-lg text-[#171A1FFF]"></i>
                        <span class="text-xl font-medium text-[#666666FF]">DAX</span>
                    </div>
                    <!-- <p class="text-sm text-gray-500">Day gig streak</p> -->
                </button>

                <button type="button" @click="goToSchedule()"
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar-alt text-lg text-[#232850FF]"></i>
                        <span class="text-xl font-bold text-[#171A1FFF]">{{ this.total_jobs }}</span>
                    </div>
                    <p class="text-sm text-[#666666FF]">Jobs Booked Today</p>
                </button>

                <button type="button"
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-thumbs-up text-lg text-[#171A1FFF]"></i>
                        <span class="text-sm font-medium text-[#666666FF]">New Job Request Dryer, no Heat, Stuart</span>
                    </div>
                    <!-- <p class="text-sm text-gray-500">New Job Request Dryer, no Heat, Stuart</p> -->
                </button>

                <button type="button"
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <p class="text-sm text-[#666666FF]">Earnings</p>
                    <div class="flex items-center space-x-2">
                        <span class="text-xl font-bold text-[#171A1FFF]">$2150</span>
                        <i class="fas fa-arrow-up text-green-500 text-sm"></i>
                    </div>
                </button>
            </div>
        </div>

        <!-- Latest Updates Section -->
        <div class="max-w-lg mx-auto p-6">
            <h2 class="text-xl text-[#171A1FFF]">Latest Updates</h2>

            <div class="space-y-4 mt-3">


                <!-- Display message when there are no updates -->
                <div v-if="latestUpdates.length === 0" class="text-center text-gray-500">
                    No gig found.
                </div>

                <div v-else v-for="(update, index) in latestUpdates" :key="index"
                    class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col space-y-2">
                    <div class="flex items-start space-x-3 cursor-pointer" @click="goToGig(update.gig_id)">
                        <!-- Image/Icon -->
                        <img v-if="update.image" :src="update.image" class="w-10 rounded-md" />
                        <i v-else :class="update.icon" class="text-3xl text-gray-700"></i>

                        <!-- Content -->
                        <div class="flex-1">
                            <h3 class="text-normal font-medium text-[#222222FF]">{{ update.title }}</h3>
                            <p class="text-sm text-[#666666FF]">{{ update.description }}</p>
                        </div>
                    </div>

                    <hr class="border-gray-300 my-2" />

                    <!-- Bottom Section: Icons on Left, Arrow Down on Right -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4 items-center">
                            <span class="text-green-500 font-bold text-lg flex items-center">
                                <!-- <i class="fas fa-dollar-sign mr-1"></i>  -->
                                {{ update.amount }}
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
                            <!-- This is additional information about "{{ update.title }}". You can add more details here. -->
                            {{ update.repair_notes ?? `This is additional information about "${update.title}". You can
                            add more details here.` }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="max-w-lg mx-auto p-6 text-center">
            <a>
                <i
                    class="fas fa-chevron-down text-xl text-gray-500 cursor-pointer transition-transform duration-300"></i>
            </a>
        </div> -->

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
                            <p class="text-normal font-normal text-gray-800">{{ update.title }}</p>
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
import axios from "axios"; // Ensure axios is imported

export default {
    components: { NavBar, BottomNav },
    name: "DashboardIndex",
    data() {
        return {
            expandedIndex: null,
            expandedIndexV2: null,
            previewPhoto: '/images/avatar.png',

            user_id: null,
            total_jobs: 0,
            name: "--",
            professionalTitle: "--",
            latestUpdates: [],
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
            ],
            gigHistoryData: [], // Store the fetched gig history
            loadingGigHistory: true, // Loading state
        };
    },
    computed: {
        formattedDate() {
            const today = new Date();
            const options = { month: "long", day: "numeric" };
            return today.toLocaleDateString("en-US", options);
        }
    },
    created() {

        this.fetchUserData().then(() => {
            console.log("User ID after fetchUserData:", this.user_id);
            this.gigHistory(); // Now it will have the correct user_id
        });
    },

    methods: {
        toggleExpand(index) {
            this.expandedIndex = this.expandedIndex === index ? null : index;
        },
        toggleExpandV2(index) {
            this.expandedIndexV2 = this.expandedIndexV2 === index ? null : index;
        },
        goToGig(id) {
            this.$router.push(`/gig/${id}`);
        },
        goToSchedule() {
            this.$router.push(`/schedules`);
        },
        async gigHistory() {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                console.log("Fetching Gig History...");

                console.log("User ID: " + this.user_id );

                const response = await axios.post(`${api_endpoint}/gigs/retrieveGigByTechID.php`,
                    { techID: this.user_id }, // Passing techID in the request body
                    {
                        headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                    }
                );

                this.gigHistoryData = response.data.data || []; // Store fetched data

                console.log(`Gig History`, response );

                this.loadingGigHistory = false; // Stop loading

                if (this.gigHistoryData.length > 0) {
                    // Transform data for latestUpdates
                    this.latestUpdates = this.gigHistoryData.map(gig => ({
                        gig_id: gig.gig_id,
                        image: gig.machine.machine_photo ? gig.machine.machine_photo : "../../../../images/washing-machine.png", // Keeping static image
                        title: `Gig #${gig.gig_cryptic} - ${gig.machine.brand_name} ${gig.machine.machine_type}`,
                        description: gig.initial_issue || "No issue description available.",
                        amount: `$${gig.gig_price}`, // Format price
                        repair_notes: `${gig.repair_notes}`
                    }));
                } else {

                    this.latestUpdates = []; // Set to empty array if no data
                }

                console.log("Transformed latestUpdates:", this.latestUpdates);

            } catch (error) {
                console.error("Error fetching gig history data:", error);
                this.loadingGigHistory = false;
            }
        },
        async fetchUserData() {
            const token = localStorage.getItem('token');
            if (!token) {
                console.error("No token found in localStorage");
                return;
            }
            try {
                const response = await axios.get('/api/user', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                const userData = response.data.user;

                // Check if profile_photo exists and is valid
                if (userData.profile_photo && userData.profile_photo !== "null") {
                    this.previewPhoto = userData.profile_photo.startsWith("http")
                        ? userData.profile_photo
                        : `${process.env.VUE_APP_BASE_URL}/storage/${userData.profile_photo}`;
                } else {
                    this.previewPhoto = "/images/avatar.png";
                }

                // Set user info
                this.name = userData.name;
                this.professionalTitle = userData.professional_title;
                this.user_id = userData.id;
                this.total_jobs = response.data.total_jobs_booked;
            } catch (error) {
                console.error("Error fetching user data:", error);
            }
        },
    }
};

</script>
