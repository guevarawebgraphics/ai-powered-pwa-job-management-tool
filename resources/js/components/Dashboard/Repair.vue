<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Repair Details Section -->
        <div class="max-w-lg mx-auto p-6">
            <h2 class="text-lg text-center text-[#232850]">
                {{ this.selectedRepair.title }}
            </h2>
            <a href="#" class="block text-center text-blue-500 underline"
                @click.prevent="goToModel(machineData.machine_id)">{{ this.gigData.model_number }}</a>

            <div class="flex items-start mt-4 relative">
                <p class="text-sm font-semibold  flex flex-col space-y-1">
                    <span class="flex items-center space-x-1 text-gray-600">
                        <i class="fas fa-wrench text-sm text-gray-700"></i>
                        <span>Most Common Repair</span>
                    </span>
                    <span class="flex items-center space-x-1 text-red-600">
                        <i data-tooltip-target="tooltip-symptoms" data-tooltip-style="light"
                            class="fas fa-info-circle text-gray-700 text-sm"></i>
                        <span>{{ this.selectedRepair.symptoms }}</span>
                    </span>
                    <span class="flex items-center space-x-1 text-green-600">
                        <i data-tooltip-target="tooltip-solution" data-tooltip-style="light"
                            class="fas fa-check text-gray-700 text-sm"></i>
                        <span>{{ this.selectedRepair.solution }}</span>
                    </span>

                </p>

                <!-- Tooltip for Symptoms -->
                <div id="tooltip-symptoms" role="tooltip"
                    class="absolute z-10 invisible px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg shadow-md opacity-0 tooltip">
                    Symptoms
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>

                <!-- Tooltip for Solution -->
                <div id="tooltip-solution" role="tooltip"
                    class="absolute z-10 invisible px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg shadow-md opacity-0 tooltip">
                    Solution
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>



            <hr class="border-gray-300 my-4" />

            <!-- Gig Stats -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <i class="fas fa-headset text-2xl text-gray-700"></i>
                    <p class="text-sm font-medium mt-2">DAX</p>
                </div>
                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <p class="text-lg font-bold text-gray-700">#{{ this.selectedRepair.id }}</p>
                </div>
            </div>

            <!-- Diagnostic Videos -->
            <h3 class="text-lg text-center mt-6 text-[#232850FF]">
                Diagnostic Videos
            </h3>
            <div class="space-y-4 mt-3">
                <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                </div>
                <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                </div>
            </div>

            <!-- Common Repair Videos -->
            <h3 class="text-lg text-center mt-6 text-[#232850FF]">
                Common Repair Videos
            </h3>
            <div class="space-y-4 mt-3">
                <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                </div>
                <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                </div>
            </div>

            <!-- Other Useful Links -->
            <h3 class="text-lg text-center mt-6 text-[#232850FF]">
                Other Useful Links
            </h3>
            <div class="space-y-4 mt-3">
                <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
                    <i class="fas fa-file-alt text-xl text-gray-700"></i>
                    <div class="ml-3">
                        <p class="text-sm text-[#222222FF]">Internal Notes</p>
                        <p class="text-xs text-gray-600">
                            Difficult UI removal on this model
                        </p>
                        <p class="text-xs text-gray-600">
                            Most common repair is Heating Element
                        </p>
                    </div>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
                    <i class="fas fa-file-alt text-xl text-gray-700"></i>
                    <div class="ml-3">
                        <p class="text-sm text-[#222222FF]">REDDIT</p>
                        <p class="text-xs text-gray-600">
                            Difficult UI removal on this model
                        </p>
                        <p class="text-xs text-gray-600">
                            Most common repair is Heating Element
                        </p>
                    </div>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
                    <i class="fas fa-file-alt text-xl text-gray-700"></i>
                    <div class="ml-3">
                        <p class="text-sm text-[#222222FF]">GUILD CHAT #68</p>
                        <p class="text-xs text-gray-600">
                            Difficult UI removal on this model
                        </p>
                        <p class="text-xs text-gray-600">
                            Most common repair is Heating Element
                        </p>
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

import axios from "axios"; // Ensure ax

export default {
    components: { NavBar, BottomNav },
    name: "GigRepairPage",
    data() {
        return {
            gigId: null,
            repairId: null,
            repairData: [],
            gigData: [],
            machineData: [],
            selectedRepair:[]
        };
    },
    created() {
        this.gigId = this.$route.params.gigId;
        this.repairId = this.$route.params.repairId;
        if (this.gigId) {
            this.gigDetail(this.gigId);
        }
    },
    watch: {
        // Watch for changes in route (if navigating to another customer)
        '$route.params.gigId'(gigID) {
            this.gigID = gigID;
        },
        '$route.params.repairId'(repairId) {
            this.repairId = repairId;
        }
    },
    methods: {
        async gigDetail(gigId) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                const response = await axios.get(`${api_endpoint}/gigs/retrieveGigByGigID.php?gig_id=${gigId}`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

                this.gigData = response.data.data[0];

                this.repairDetail(this.gigData.machine_id, this.repairId);

                console.log(this.gigData);

            } catch (error) {
                console.error("Error fetching gig history data:", error);
            }
        },
        async repairDetail(machineID, repairId) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                const response = await axios.get(`${api_endpoint}/machines/retrieveMachineByID.php?machine_id=${machineID}`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

                this.machineData = response.data.data;

                if (this.machineData && this.machineData.common_repairs) {
                    let commonRepairs = [];

                    try {
                        commonRepairs = JSON.parse(this.machineData.common_repairs); // Parse JSON string
                    } catch (error) {
                        console.error("Error parsing common_repairs JSON:", error);
                        return;
                    }

                    // Debugging: Log the parsed array and repairId
                    console.log("Parsed common_repairs:", commonRepairs);
                    console.log("repairId Type:", typeof repairId, "Value:", repairId);

                    // Ensure repairId is a number for comparison
                    const numericRepairId = Number(repairId);

                    if (Array.isArray(commonRepairs)) {
                        const repair = commonRepairs.find(repair => Number(repair.id) === numericRepairId);

                        if (repair) {
                            console.log("Matching repair:", repair);
                            this.selectedRepair = repair;
                        } else {
                            console.log("Repair not found for ID:", numericRepairId);
                        }
                    } else {
                        console.log("Parsed common_repairs is not an array:", commonRepairs);
                    }
                } else {
                    console.log("common_repairs is missing or null:", this.machineData);
                }
            } catch (error) {
                console.error("Error fetching repair data:", error);
            }
        },
        goToModel(modelID) {
            this.$router.push(`/model/${modelID}`);
        }
    }
};
</script>
