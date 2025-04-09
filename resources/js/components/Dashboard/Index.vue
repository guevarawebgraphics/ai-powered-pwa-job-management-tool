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
                <h2 class="text-xl text-[#171A1FFF]">{{ formattedDate }}</h2>
                <span class="text-gray-500 text-sm">Last {{ getLastDaysRange }} days</span>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3">


                <DAX />

                <button type="button" @click="goToSchedule()"
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar-alt text-lg text-[#232850FF]"></i>
                        <!-- <span class="text-xl font-bold text-[#171A1FFF]">{{ this.total_jobs }}</span> -->

                        <span class="text-xl font-bold text-[#171A1FFF]">{{
                            this.totalJobBookedToday
                            }}</span>
                    </div>
                    <p class="text-sm text-[#666666FF]">Jobs Booked Today</p>
                </button>

                <button @click="goToNotification()" type="button"
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex flex-col sm:flex-row items-center sm:space-x-4 space-y-2 sm:space-y-0">
                        <!-- Left Column: Icon and Price stacked vertically -->
                        <div class="flex flex-col items-center">
                            <i class="fas fa-thumbs-up text-xl text-[#171A1FFF]"></i>
                            <div class="text-xl font-bold" v-if="hasFeaturedContent">
                                ${{ parsedFeaturedContent?.gig_price }}
                            </div>
                        </div>

                        <!-- Right Column: Notification description -->
                        <div class="text-center sm:text-left">
                            <div class="text-gray-700" v-if="hasFeaturedContent">
                                {{ parsedFeaturedContent?.initial_issue }}
                            </div>
                            <div v-else>
                                No Latest Notification
                            </div>
                        </div>
                    </div>

                    <!-- <p class="text-sm text-gray-500">New Job Request Dryer, no Heat, Stuart</p> -->
                </button>

                <button type="button"
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <p class="text-sm text-[#666666FF]">Earnings</p>
                    <div class="flex items-center space-x-2">
                        <span class="text-xl font-bold text-[#171A1FFF]">${{ this.totalGigPrice }}</span>
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
                            <h3 class="text-normal font-medium text-[#222222FF]">
                                {{ update.title }}
                            </h3>
                            <p class="text-sm text-[#666666FF]">
                                {{ update.description }}
                            </p>
                        </div>
                    </div>

                    <hr class="border-gray-300 my-2" />

                    <!-- Bottom Section: Icons on Left, Arrow Down on Right -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4 items-center">
                            <span class="text-green-500 font-bold text-lg flex items-center">
                                <!-- <i class="fas fa-dollar-sign mr-1"></i>  -->
                                ${{ update.potentialGigPrice }}
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
                    <div v-if="expandedIndex === index" class="mt-2 p-2 rounded-md">
                        <ul>
                            <li v-if="
                                    update.machine &&
                                    update.machine.common_repairs
                                ">
                                <span class="text-[#66B2ECFF] cursor-pointer">
                                    <i class="fas fa-info-circle text-xl text-[#171A1FFF]"></i>&nbsp;{{
                                    firstRepair(
                                    update.machine.common_repairs
                                    )
                                    }}</span>
                            </li>

                            <li v-if="update.youtube_link" class="mt-2">
                                <a :href="update.youtube_link" target="_blank"
                                    class="cursor-pointer text-[#66B2ECFF]"><i
                                        class="fas fa-play-circle text-xl text-[#171A1FFF]"></i>
                                    {{ update.youtube_link }}
                                </a>
                            </li>
                            <li class="mt-2">
                                <button type="button" @click="
                                        goToModel(update.machine.model_number)
                                    " class="text-[#66B2ECFF]">
                                    <i class="fas fa-book text-xl text-[#171A1FFF]"></i>
                                    Service Manual
                                </button>
                            </li>
                        </ul>
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
            <h2 class="text-xl font-medium text-[#171A1FFF]">
                Earn While you Learn
            </h2>

            <div class="space-y-4 mt-3">
                <div v-for="(update, index) in earnWhileYouLearn" :key="index"
                    class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col space-y-2">
                    <div class="flex items-start space-x-3">
                        <!-- Image/Icon -->
                        <img v-if="update.image" :src="update.image" class="w-12 h-12 rounded-md" />
                        <i v-else :class="update.icon" class="text-3xl text-gray-700"></i>

                        <!-- Content -->
                        <div class="flex-1">
                            <p class="text-normal font-normal text-gray-800">
                                {{ update.title }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ update.description }}
                            </p>
                        </div>
                    </div>

                    <hr class="border-gray-300 my-2" />

                    <!-- Bottom Section: Icons on Left, Arrow Down on Right -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4 items-center">
                            <span class="text-green-500 font-bold text-lg flex items-center">
                                <i class="fas fa-dollar-sign mr-1"></i>
                                {{ update.amount }}
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
                            This is additional information about "{{
                            update.title
                            }}". You can add more details here.
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
import Ratings from "../sections/Ratings.vue";
import BottomNav from "../sections/Bottombar.vue";
import DAX from "../sections/DAX.vue";
import axios from "axios"; // Ensure axios is imported

export default {
    components: { NavBar, BottomNav, Ratings, DAX },
    name: "DashboardIndex",
    data() {
        return {
            serviceManual: [],
            noServiceManualMessage: null,
            expandedIndex: null,
            expandedIndexV2: null,
            previewPhoto: "/images/avatar.png",

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
            totalGigPrice: 0.0,
            totalJobBookedToday: 0,
            latestNotif: {
                type: Object,
                default: null
            },
            selectedDate: new Date().toISOString().substr(0, 10),
            selectedTime: new Date().toISOString().substr(11, 5),
        };
    },
    computed: {
        parsedFeaturedContent() {
            if (this.latestNotif && this.latestNotif.featured_content) {
                // If it's already an object, return it directly.
                if (typeof this.latestNotif.featured_content === 'object') {
                    return this.latestNotif.featured_content;
                }
                // Otherwise, try parsing it as JSON.
                try {
                    return JSON.parse(this.latestNotif.featured_content);
                } catch (error) {
                    console.error("Failed to parse featured_content JSON:", error);
                    return {};
                }
            }
            return {};
        },
        hasFeaturedContent() {
            // Check that we have valid data (non-empty object) and that latestNotif exists.
            return (
                this.latestNotif &&
                this.latestNotif.featured_content &&
                Object.keys(this.parsedFeaturedContent).length > 0
            );
        },
        formattedDate() {
            const today = new Date();
            const options = { month: "long", day: "numeric", timeZone: "UTC" };
            return new Intl.DateTimeFormat("en-US", options).format(today);
        },
        getLastDaysRange() {
            const today = new Date();
            return today.getDate() - 1; // Subtracting 1 because we don't count today itself
        },
    },
    created() {
        this.fetchUserData().then(() => {
            console.log("User ID after fetchUserData:", this.user_id);
            this.gigHistory(); // Now it will have the correct user_id
        });
    },

    methods: {
        capitalizeWords(str) {
            if (!str) return ""; // Return an empty string if str is undefined/null
            return str.replace(/\b\w/g, (char) => char.toUpperCase());
        },
        toggleExpand(index) {
            this.expandedIndex = this.expandedIndex === index ? null : index;
        },
        toggleExpandV2(index) {
            this.expandedIndexV2 =
                this.expandedIndexV2 === index ? null : index;
        },
        goToGig(id) {
            this.$router.push(`/gig/${id}`);
        },
        goToSchedule() {
            this.$router.push(`/schedules`);
        },
        goToModel(modelNumber) {
            this.$router.push(`/model/${modelNumber}`);
        },
        goToNotification() {
            this.$router.push(`/notification`);
        },
        async gigHistory() {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                console.log("Fetching Gig History...");

                console.log("User ID: " + this.user_id);

                const payload = {
                    techID: this.user_id, // Replace with dynamic techID if needed
                    current_datetime: new Date()
                        .toISOString()
                        .slice(0, 19)
                        .replace("T", " "),
                    total_records: 5, // Limit to 5
                };

                // alert(this.selectedDate);
                if (this.selectedDate) {
                    payload.date = this.selectedDate; // Add selected date to request if available
                }

                if (this.selectedTime) {
                    payload.time = this.selectedTime;
                }

                console.log(`Selected Date: ${this.selectedDate}`);
                console.log(`Selected Time: ${this.selectedTime}`);

                const response = await axios.post(
                    `${api_endpoint}/gigs/retrieveGigByTechID.php`,
                    payload, // Passing techID in the request body
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "application/json",
                        },
                    }
                );

                this.gigHistoryData = response.data.data || []; // Store fetched data

                console.log(`Gig History`, response);

                this.loadingGigHistory = false; // Stop loading

                if (this.gigHistoryData.length > 0) {
                    
                    this.totalJobBookedToday = this.gigHistoryData.length;

                    // Transform data for latestUpdates
                    this.latestUpdates = this.gigHistoryData.map((gig) => {



                        // Determine upsell availability: upsell is available if the client is missing one or both plans.
                        const hasUpsellPotential = !(gig.insurance_plan && gig.maintenance_plan);

                        // Determine the basic rate based on technician rank
                        const basic_rate = gig.tech_rank_type === "0" ? 40.00 : 50.00;

                        // Empty the earnings array
                        this.gigPotentialEarnings = [];

                        // First-time visit logic
                        if (gig.gig_type == "0") {
                            // Diagnostic only scenario
                            this.gigPotentialEarnings.push({ name: "Diagnostic only", amount: basic_rate });
                            // Diagnostic + Upsell scenario (only if upsell is applicable)
                            if (hasUpsellPotential) {
                                this.gigPotentialEarnings.push({ name: "Diagnostic + upsell", amount: basic_rate + 25.00 });
                            }
                            // Full repair onsite scenario (which is equivalent to doing both diagnostic and repair in one visit)
                            this.gigPotentialEarnings.push({ name: "Full repair onsite", amount: basic_rate * 2 });
                            // Full repair onsite with upsell (if upsell is applicable)
                            if (hasUpsellPotential) {
                                this.gigPotentialEarnings.push({ name: "Full repair onsite + upsell", amount: (basic_rate * 2) + 25.00 });
                            }
                        }
                        // Return visit logic
                        else if (gig.gig_type == "1") {
                            // Return repair scenario
                            this.gigPotentialEarnings.push({ name: "Return repair", amount: basic_rate });
                            // Return repair with upsell scenario (if upsell is applicable)
                            if (hasUpsellPotential) {
                                this.gigPotentialEarnings.push({ name: "Return repair + upsell", amount: basic_rate + 25.00 });
                            }
                        }

                        // Calculate the potential gig price by summing the amounts of all scenarios
                        // const potentialGigPrice = this.gigPotentialEarnings.reduce((total, item) => total + item.amount, 0);
                        let potentialGigPrice = 0.00;
                        if (this.gigPotentialEarnings.length > 0) {
                            potentialGigPrice = Math.max(...this.gigPotentialEarnings.map(item => item.amount));
                        }

                        return {
                            gig_id: gig.gig_id,
                            image: gig.machine.machine_photo
                                ? gig.machine.machine_photo
                                : "../../../../images/washing-machine.png", // Keeping static image
                            title: `Gig #${this.capitalizeWords(gig.gig_cryptic)} - ${this.capitalizeWords(gig.machine.brand_name)} ${this.capitalizeWords(gig.machine.machine_type)}`,
                            description: gig.initial_issue || "No issue description available.",
                            amount: `$${gig.gig_price}`, // Format price
                            repair_notes: `${gig.repair_notes}`,
                            machine: gig.machine,
                            youtube_link: gig.youtube_link,
                            potentialGigPrice: potentialGigPrice  // New custom field
                        };
                    });

                    this.totalGigPrice = this.latestUpdates.reduce(
                        (sum, gig) => sum + parseFloat(gig.potentialGigPrice || 0.00),
                        0.0
                    );

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
                this.user_id = userData.id;
                this.total_jobs = response.data.total_jobs_booked;
                this.latestNotif = response.data.latest_notif;
                console.log(`latest notif: `, this.latestNotif);
            } catch (error) {
                console.error("Error fetching user data:", error);
            }
        },
        firstRepair(data) {
            try {
                const repairs = JSON.parse(data);
                return Array.isArray(repairs) && repairs.length > 0
                    ? `${repairs[0].repairName} - ${repairs[0].solution}`
                    : null;
            } catch (e) {
                return null;
            }
        },
    },
};
</script>
