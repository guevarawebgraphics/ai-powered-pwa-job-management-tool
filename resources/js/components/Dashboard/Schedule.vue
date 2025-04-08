<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Statistics Section -->
        <div class="max-w-lg mx-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl text-[#171A1FFF]">{{ formattedDate }}</h2>

                <!-- Date Picker -->
                <div class="relative">
                    <input
                        type="date"
                        v-model="selectedDate"
                        @change="gigHistory"
                        class="border border-gray-600 text-gray-700 text-sm px-3 py-1.5 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300"
                >
                    <div class="flex flex-col items-center space-y-2">
                        <i class="fas fa-headset text-xl text-[#171A1FFF]"></i>
                        <span class="text-sm text-[#666666FF]">DAX</span>
                    </div>
                </div>

                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300"
                >
                    <div class="flex items-center space-x-2">
                        <span class="text-xl font-bold text-green-500"
                            >${{ this.totalGigPrice }}</span
                        >
                    </div>
                    <p class="text-sm text-[#666666FF]">
                        Today's Potential Earnings
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-lg mx-auto p-6">
            <h2 class="text-xl text-[#171A1FFF]">Latest Updates</h2>
            <div class="space-y-6 mt-3">
                <!-- Show message if no gigs -->
                <div
                    v-if="Object.keys(groupedUpdates).length === 0"
                    class="text-center text-gray-500"
                >
                    No gig found.
                </div>

                <!-- Loop through dates -->
                <div v-else v-for="(hours, date) in groupedUpdates" :key="date">
                    <h3 class="text-lg font-normal text-[#171A1FFF] mt-4">
                        {{ date }}
                    </h3>
                    <!-- Loop through time slots within each date -->
                    <div v-for="(updates, hour) in hours" :key="hour">
                        <h4 class="text-md font-normal text-[#171A1FFF] mt-2">
                            {{ hour }}
                        </h4>
                        <div class="space-y-4 mt-2">
                            <!-- Loop through each update in that time slot -->
                            <div
                                v-for="(update, index) in updates"
                                :key="index"
                                class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col space-y-2"
                            >
                                <div
                                    class="flex items-start space-x-3 cursor-pointer"
                                    @click="goToGig(update.gig_id)"
                                >
                                    <!-- Display image or icon -->
                                    <img
                                        v-if="update.image"
                                        :src="update.image"
                                        class="w-10 rounded-md"
                                    />
                                    <i
                                        v-else
                                        :class="update.icon"
                                        class="text-3xl text-gray-700"
                                    ></i>
                                    <!-- Update content -->
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

                                <!-- Bottom section with icons and expand toggle -->
                                <div class="flex justify-between items-center">
                                    <div class="flex space-x-4 items-center">
                                        <span
                                            class="text-green-500 font-bold text-lg flex items-center"
                                            >${{ update.potentialGigPrice }}</span
                                        >
                                        <i
                                            class="fas fa-thumbs-up text-xl text-[#171A1FFF]"
                                        ></i>
                                        <i
                                            class="fas fa-play-circle text-xl text-[#171A1FFF]"
                                        ></i>
                                        <i
                                            class="fas fa-info-circle text-xl text-[#171A1FFF]"
                                        ></i>
                                    </div>
                                    <i
                                        @click="toggleExpand(date, hour, index)"
                                        class="fas fa-chevron-down text-xl text-gray-500 cursor-pointer transition-transform duration-300"
                                        :class="{
                                            'rotate-180':
                                                expandedIndex ===
                                                `${date}-${hour}-${index}`,
                                        }"
                                    ></i>
                                </div>

                                <!-- Expanded details -->
                                <div
                                    v-if="
                                        expandedIndex ===
                                        `${date}-${hour}-${index}`
                                    "
                                    class="mt-2 p-2 rounded-md"
                                >
                                    <ul>
                                        <li
                                            v-if="
                                                update.machine &&
                                                update.machine.common_repairs
                                            "
                                        >
                                            <span
                                                class="text-[#66B2ECFF] cursor-pointer"
                                            >
                                                <i
                                                    class="fas fa-info-circle text-xl text-[#171A1FFF]"
                                                ></i
                                                >&nbsp;
                                                {{
                                                    firstRepair(
                                                        update.machine
                                                            .common_repairs
                                                    )
                                                }}
                                            </span>
                                        </li>
                                        <li
                                            v-if="update.youtube_link"
                                            class="mt-2"
                                        >
                                            <a
                                                :href="update.youtube_link"
                                                target="_blank"
                                                class="cursor-pointer text-[#66B2ECFF]"
                                            >
                                                <i
                                                    class="fas fa-play-circle text-xl text-[#171A1FFF]"
                                                ></i>
                                                {{ update.youtube_link }}
                                            </a>
                                        </li>
                                        <li class="mt-2">
                                            <button
                                                type="button"
                                                @click="
                                                    goToModel(
                                                        update.machine
                                                            .model_number
                                                    )
                                                "
                                                class="text-[#66B2ECFF]"
                                            >
                                                <i
                                                    class="fas fa-book text-xl text-[#171A1FFF]"
                                                ></i>
                                                Service Manual
                                            </button>
                                        </li>
                                    </ul>
                                </div>
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
            previewPhoto: "/images/avatar.png",
            user_id: null,
            total_jobs: 0,
            name: "--",
            professionalTitle: "--",
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
            formattedDate: "",
            totalGigPrice: 0.0,
            gigPotentialEarnings : []

        };
    },
    computed: {
        // Group updates by time dynamically
        groupedUpdates() {
            return this.latestUpdates.reduce((groups, update) => {
                // Convert rawTime to a proper ISO UTC format if needed.
                // For example, if the rawTime is "2025-04-03 10:20:30", convert it to "2025-04-03T10:20:30Z"
                let rawTime = update.rawTime;
                if (rawTime && rawTime.indexOf("T") === -1) {
                    rawTime = rawTime.replace(" ", "T") + "Z";
                }
                const updateDate = new Date(rawTime);
                if (isNaN(updateDate)) {
                    console.error("Invalid date:", update.rawTime);
                    return groups;
                }
                // Group by UTC date: "Month Day" (e.g., "April 4")
                const dateStr = updateDate.toLocaleDateString("en-US", {
                    timeZone: "UTC",
                    month: "long",
                    day: "numeric",
                });
                // Group by UTC time: "h:mm AM/PM" (e.g., "5:00 AM")
                const timeStr = updateDate.toLocaleTimeString("en-US", {
                    timeZone: "UTC",
                    hour: "numeric",
                    minute: "2-digit",
                    hour12: true,
                });
                if (!groups[dateStr]) {
                    groups[dateStr] = {};
                }
                if (!groups[dateStr][timeStr]) {
                    groups[dateStr][timeStr] = [];
                }
                groups[dateStr][timeStr].push(update);
                return groups;
            }, {});
        },

        formattedDate() {
            if (this.selectedDate) {
                // Append time to ensure correct UTC parsing if the value is in "YYYY-MM-DD" format
                const date = new Date(this.selectedDate + "T00:00:00Z");
                const options = {
                    month: "long",
                    day: "numeric",
                    timeZone: "UTC",
                };
                return new Intl.DateTimeFormat("en-US", options).format(date);
            } else {
                // Default to today's date if no date is selected
                const today = new Date();
                const options = {
                    month: "long",
                    day: "numeric",
                    timeZone: "UTC",
                };
                return new Intl.DateTimeFormat("en-US", options).format(today);
            }
        },
        // formattedDate() {
        //     const date = new Date();
        //     const options = { month: 'long', day: 'numeric', timeZone: "UTC" };
        //     return date.toLocaleDateString('en-US', options);
        // }
    },
    mounted() {
        const date = new Date();
        const options = { month: "long", day: "numeric" };
        this.formattedDate = date.toLocaleDateString("en-US", options);
    },
    created() {
        this.fetchUserData().then(() => {
            console.log("User ID after fetchUserData:", this.user_id);
            this.gigHistory(); // Now it will have the correct user_id
        });
    },
    methods: {
        goToGig(id) {
            this.$router.push(`/gig/${id}`);
        },

        toggleExpand(date, time, index) {
            // Convert time & index into a unique identifier
            const itemKey = `${date}-${time}-${index}`;

            // If the clicked item is already open, close it
            // Otherwise, close all and open the clicked one
            this.expandedIndex =
                this.expandedIndex === itemKey ? null : itemKey;
        },

        // async gigHistory() {
        //     // try {
        //         const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
        //         const token = import.meta.env.VITE_API_KEY;

        //         console.log("Fetching Gig History...");

        //         const payload = {
        //             techID: this.user_id,   // Replace with dynamic techID if needed
        //             current_datetime: new Date()
        //                 .toISOString()
        //                 .slice(0, 19)
        //                 .replace("T", " ")
        //         };
        //         // alert(this.selectedDate);
        //         if (this.selectedDate) {
        //             payload.date = this.selectedDate; // Add selected date to request if available
        //         }

        //         const response = await axios.post(`${api_endpoint}/gigs/retrieveGigByTechID.php`,
        //             payload, // Passing techID in the request body
        //             {
        //                 headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
        //             }
        //         );

        //         this.gigHistoryData = response.data.data || []; // Store fetched data

        //         console.log(`${response}`);

        //         if (this.gigHistoryData.length > 0) {

        //             this.totalGigPrice = this.gigHistoryData.reduce((sum, gig) => sum + parseFloat(gig.gig_price || 0.00), 0.00);

        //             // Transform data for latestUpdates
        //             this.latestUpdates = this.gigHistoryData.map(gig => {
        //                 let recommendedRepairs = [];

        //                 // Parse `top_recommended_repairs` if it's a valid JSON string
        //                 try {
        //                     recommendedRepairs = gig.top_recommended_repairs
        //                         ? Object.values(JSON.parse(gig.top_recommended_repairs))
        //                         : [];
        //                 } catch (error) {
        //                     console.error("Error parsing recommended repairs:", error);
        //                 }

        //                 // Extract time from start_datetime
        //                 const gigTime = new Date(gig.start_datetime).toLocaleTimeString("en-US", {
        //                     hour: "numeric",
        //                     minute: "2-digit",
        //                     hour12: true
        //                 });

        //                 return {
        //                     gig_id: gig.gig_id,
        //                     time: gigTime, // Group by gig time
        //                     image: gig.machine.machine_photo ? gig.machine.machine_photo : "../../../../images/washing-machine.png", // Static image
        //                     title: `Gig #${gig.gig_cryptic} - ${gig.machine_brand} ${gig.appliance_type}`,
        //                     description: gig.initial_issue || "No issue description available.",
        //                     amount: `$${gig.gig_price}`, // Format price
        //                     repair_notes: gig.repair_notes || "No repair notes available.",
        //                     recommended_repairs: recommendedRepairs.join(", ") || "No recommended repairs.",
        //                     machine: gig.machine,
        //                     youtube_link: gig.youtube_link
        //                 };
        //             });

        //         } else {
        //             this.totalGigPrice = 0.00;
        //             this.latestUpdates = []; // Set to empty array if no data
        //         }

        //         console.log("Transformed latestUpdates:", this.latestUpdates);

        //     // } catch (error) {
        //     //     console.error("Error fetching gig history data:", error);
        //     // }
        // },

        async gigHistory() {
            const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
            const token = import.meta.env.VITE_API_KEY;

            console.log("Fetching Gig History...");

            const payload = {
                techID: this.user_id,
                current_datetime: new Date()
                    .toISOString()
                    .slice(0, 19)
                    .replace("T", " "),
            };

            if (this.selectedDate) {
                payload.date = this.selectedDate;
            }

            try {
                const response = await axios.post(
                    `${api_endpoint}/gigs/retrieveGigByTechID.php`,
                    payload,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "application/json",
                        },
                    }
                );

                this.gigHistoryData = response.data.data || [];
                console.log("Response:", response);

                if (this.gigHistoryData.length > 0) {

                    const todayUTC = new Date().toISOString().split("T")[0]; // e.g., "2025-04-05"

                    this.totalGigPrice = this.gigHistoryData
                        .filter((gig) => {
                            const gigDateUTC = new Date(
                                gig.start_datetime.replace(" ", "T") + "Z"
                            )
                                .toISOString()
                                .split("T")[0];
                            return gigDateUTC === todayUTC;
                        })
                        .reduce((sum, gig) => sum + parseFloat(gig.gig_price || 0), 0);


                    // Transform the data for latestUpdates.
                    // Also, adjust the rawTime into a proper ISO UTC string if needed.
                    this.latestUpdates = this.gigHistoryData.map((gig) => {
                        let recommendedRepairs = [];
                        try {
                            recommendedRepairs = gig.top_recommended_repairs
                                ? Object.values(
                                      JSON.parse(gig.top_recommended_repairs)
                                  )
                                : [];
                        } catch (error) {
                            console.error(
                                "Error parsing recommended repairs:",
                                error
                            );
                        }

                        // Adjust rawTime if needed. For example, convert "YYYY-MM-DD HH:mm:ss" to ISO "YYYY-MM-DDTHH:mm:ssZ"
                        let rawTime = gig.start_datetime;
                        if (rawTime && rawTime.indexOf("T") === -1) {
                            rawTime = rawTime.replace(" ", "T") + "Z";
                        }

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
                        const potentialGigPrice = this.gigPotentialEarnings.reduce((total, item) => total + item.amount, 0);
    
                        return {
                            gig_id: gig.gig_id,
                            rawTime: rawTime, // Save the raw datetime in ISO UTC format
                            formattedTime: new Date(rawTime).toLocaleTimeString(
                                "en-US",
                                {
                                    hour: "numeric",
                                    minute: "2-digit",
                                    hour12: true,
                                }
                            ),
                            image: gig.machine.machine_photo
                                ? gig.machine.machine_photo
                                : "../../../../images/washing-machine.png",
                            title: `Gig #${gig.gig_cryptic} - ${gig.machine_brand} ${gig.appliance_type}`,
                            description:
                                gig.initial_issue ||
                                "No issue description available.",
                            amount: `$${gig.gig_price}`,
                            repair_notes:
                                gig.repair_notes ||
                                "No repair notes available.",
                            recommended_repairs:
                                recommendedRepairs.join(", ") ||
                                "No recommended repairs.",
                            machine: gig.machine,
                            youtube_link: gig.youtube_link,
                            potentialGigPrice: potentialGigPrice,
                            start_datetime: gig.start_datetime,
                            gig_price:gig.gig_price
                        };
                    });


                    this.totalGigPrice = this.latestUpdates
                    .filter((gig) => {
                        const gigDateUTC = new Date(
                            gig.start_datetime.replace(" ", "T") + "Z"
                        )
                            .toISOString()
                            .split("T")[0];
                        return gigDateUTC === todayUTC;
                    })
                        .reduce((sum, gig) => sum + parseFloat(gig.potentialGigPrice || 0), 0);

                } else {
                    this.totalGigPrice = 0;
                    this.latestUpdates = [];
                }
                console.log("Transformed latestUpdates:", this.latestUpdates);
            } catch (error) {
                console.error("Error fetching gig history data:", error);
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
            } catch (error) {
                console.error("Error fetching user data:", error);
            }
        },

        goToModel(modelNumber) {
            this.$router.push(`/model/${modelNumber}`);
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
