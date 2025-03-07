<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Statistics Section -->
        <div class="max-w-lg mx-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl text-[#171A1FFF]">June, 12</h2>

                <!-- Date Picker -->
                <div class="relative">
                    <input type="date" v-model="selectedDate"
                        class="border border-gray-600 text-gray-700 text-sm px-3 py-1.5 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex flex-col items-center space-y-2">
                        <i class="fas fa-headset text-xl text-[#171A1FFF]"></i>
                        <span class="text-sm text-[#666666FF]">DAX</span>
                    </div>
                </div>


                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <div class="flex items-center space-x-2">
                        <span class="text-xl font-bold text-green-500">$100</span>
                    </div>
                    <p class="text-sm text-[#666666FF]">
                        Today's Potential Earnings
                    </p>
                </div>
            </div>
        </div>

        <!-- Latest Updates Section -->
        <div class="max-w-lg mx-auto p-6">
            <h2 class="text-xl text-[#171A1FFF]">Latest Updates</h2>

            <div class="space-y-6 mt-3">
                <!-- Loop Through Grouped Updates by Time -->
                <div v-for="(updates, time) in groupedUpdates" :key="time">
                    <h3 class="text-lg font-normal text-[#171A1FFF] mt-4">
                        {{ time }}
                    </h3>

                    <div class="space-y-4 mt-2">
                        <div v-for="(update, index) in updates" :key="index"
                            class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col space-y-2">
                            <div class="flex items-start space-x-3">
                                <!-- Image/Icon -->
                                <img v-if="update.image" :src="update.image" class="w-12 h-12 rounded-md" />
                                <i v-else :class="update.icon" class="text-3xl text-gray-700"></i>

                                <!-- Content -->
                                <div class="flex-1">
                                    <h3 class="text-normal text-gray-800">
                                        {{ update.title }}
                                    </h3>
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
                                        <!-- <i class="fas fa-dollar-sign mr-1"></i> -->
                                        {{ update.amount }}
                                    </span>
                                    <i class="fas fa-thumbs-up text-xl text-[#171A1FFF]"></i>
                                    <i class="fas fa-play-circle text-xl text-[#171A1FFF]"></i>
                                    <i class="fas fa-info-circle text-xl text-[#171A1FFF]"></i>
                                </div>
                                <!-- Toggle Arrow -->
                                <i @click="toggleExpand(time, index)"
                                    class="fas fa-chevron-down text-xl text-gray-500 cursor-pointer transition-transform duration-300"
                                    :class="{
                                        'rotate-180': expandedIndex === `${time}-${index}`,
                                    }"></i>
                            </div>

                            <!-- Expanded Content -->
                            <div v-if="expandedIndex === `${time}-${index}`" class="mt-2 p-2 bg-gray-100 rounded-md">
                                <p class="text-sm text-gray-700">
                                   {{ update.repair_notes ?? `This is additional information about "${update.title}". You can
                            add more details here.` }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    name: "SchedulePage",
    data() {
        return {
            selectedDate: new Date().toISOString().substr(0, 10), // Default to today's date
            expandedIndex: null, // Store the currently expanded index
            latestUpdates: [
                {
                    time: "1PM",
                    image: "/images/washing-machine.png",
                    title: "Top Load Washer No Drain",
                    description:
                        "**Bring ShopVac with Cone Extension**\n*Verify Symptoms, Bring Replacement Pump*",
                    amount: "100",
                },
                {
                    time: "3PM",
                    image: "/images/washing-machine.png",
                    title: "Next Job in 3 hours.",
                    description:
                        "Be prepared and Kick Ass. Watch suggested Repair videos, wash your butt and put on the uniform..",
                    amount: "100",
                },
                {
                    time: "4PM",
                    image: "/images/washing-machine.png",
                    title: "Next Job in 3 hours.",
                    description:
                        "Be prepared and Kick Ass. Watch suggested Repair videos, wash your butt and put on the uniform..",
                    amount: "100",
                },
                {
                    time: "7PM",
                    image: "/images/washing-machine.png",
                    title: "Next Job in 3 hours.",
                    description:
                        "Be prepared and Kick Ass. Watch suggested Repair videos, wash your butt and put on the uniform..",
                    amount: "100",
                },
            ],
            gigHistoryData: [],
            latestUpdates: [],
        };
    },
    computed: {
        // Group updates by time dynamically
        groupedUpdates() {
            return this.latestUpdates.reduce((groups, update) => {
                (groups[update.time] = groups[update.time] || []).push(update);
                return groups;
            }, {});
        },
    },
    created() {
        this.gigHistory();
    },
    methods: {
        toggleExpand(time, index) {
            // Convert time & index into a unique identifier
            const itemKey = `${time}-${index}`;

            // If the clicked item is already open, close it
            // Otherwise, close all and open the clicked one
            this.expandedIndex = this.expandedIndex === itemKey ? null : itemKey;
        },


        async gigHistory() {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                console.log("Fetching Gig History...");

                const response = await axios.get(`${api_endpoint}/gigs/retrieveGigByTechID.php`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

                this.gigHistoryData = response.data.data; // Store fetched data

                console.log(`${response}`);

                // Transform data for latestUpdates
                this.latestUpdates = this.gigHistoryData.map(gig => {
                    let recommendedRepairs = [];

                    // Parse `top_recommended_repairs` if it's a valid JSON string
                    try {
                        recommendedRepairs = gig.top_recommended_repairs
                            ? Object.values(JSON.parse(gig.top_recommended_repairs))
                            : [];
                    } catch (error) {
                        console.error("Error parsing recommended repairs:", error);
                    }

                    // Extract time from start_datetime
                    const gigTime = new Date(gig.start_datetime).toLocaleTimeString("en-US", {
                        hour: "numeric",
                        minute: "2-digit",
                        hour12: true
                    });

                    return {
                        gig_id: gig.gig_id,
                        time: gigTime, // Group by gig time
                        image: "../../../../images/washing-machine.png", // Static image
                        title: `Gig #${gig.gig_cryptic} - ${gig.machine_brand} ${gig.appliance_type}`,
                        description: gig.initial_issue || "No issue description available.",
                        amount: `$${gig.gig_price}`, // Format price
                        repair_notes: gig.repair_notes || "No repair notes available.",
                        recommended_repairs: recommendedRepairs.join(", ") || "No recommended repairs."
                    };
                });

                console.log("Transformed latestUpdates:", this.latestUpdates);

            } catch (error) {
                console.error("Error fetching gig history data:", error);
            }
        }


    },
};
</script>
