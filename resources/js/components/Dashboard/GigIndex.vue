<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <div class="max-w-lg mx-auto px-4 py-6 space-y-6">
            <!-- Gig Header -->
            <div class="text-center">
                <h2 class="text-lg text-[#232850FF]">
                    <!-- Gig #R-LDNH307 -->
                    Gig #{{ this.gigData.gig_cryptic }}
                </h2>
                <div class="flex items-center justify-center space-x-3 mt-2">
                    <img :src="this.machineData.machine_photo" alt="Samsung Dryer" class="w-12 h-12 rounded-md" />
                    <div class="text-sm text-gray-600 text-left">
                        <p class="font-semibold">{{ this.gigData.machine_brand }}</p>
                        <p class="text-gray-500">
                            **{{ this.gigData.initial_issue }}**
                        </p>
                        <p class="text-gray-500">
                            *{{ this.gigData.repair_notes }} *
                        </p>
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="flex justify-between items-center">
                <h3 class="text-lg text-[#232850FF]">{{ formattedCreatedAt }}</h3>
                <span class="text-lg text-[#232850FF]">{{ formattedCreatedTime }}</span>
            </div>

            <!-- Earnings & Gig Potential -->
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <i class="fas fa-headset text-lg text-gray-700"></i>
                    <span class="text-md mt-1">DAX</span>
                </div>
                <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <span class="text-xl font-bold text-green-500">${{ this.gigData.gig_price }}</span>
                    <p class="text-sm text-gray-500">Gig Potential</p>
                </div>
            </div>

            <!-- Start/End/Submit Report Button -->
            <button :class="buttonClass" class="w-full text-white py-2 rounded-lg font-semibold"
                @click="actionGig(this.gigData.gig_id)">
                {{ buttonText }}
            </button>

            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-md border p-4 space-y-3">
                <!-- Top Section -->
                <div class="flex justify-between items-center cursor-pointer"
                    @click="goToCustomer(this.gigData.client_id)">
                    <div class="flex items-center space-x-3">
                        <!-- Profile Icon -->
                        <i class="fas fa-id-card text-gray-500 text-3xl"></i>
                        <div>
                            <p class="font-bold text-lg text-gray-900">
                                {{ this.gigData.client_name }}
                            </p>
                            <p class="text-sm text-gray-500">{{ this.gigData.client_extra_field1 }}</p>
                            <p class="text-xs text-gray-500">{{ this.gigData.client_extra_field2 }}</p>
                        </div>
                    </div>
                    <!-- Star Icon -->
                    <i class="fas fa-star text-yellow-400 text-2xl"></i>
                </div>

                <!-- Divider -->
                <hr class="border-gray-300" />

                <!-- Bottom Section -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <!-- Dollar Icon -->
                        <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                        <p class="text-green-500 font-bold text-lg">750</p>
                    </div>
                    <!-- Dropdown Arrow -->
                    <i class="fas fa-chevron-down text-gray-500"></i>
                </div>
            </div>


            <!-- Customer Location & Contact -->
            <div class="bg-white rounded-lg shadow-md border p-4 flex items-start space-x-3">
                <!-- Phone Icon -->
                <i class="fas fa-phone-alt text-gray-500 text-3xl"></i>
                <div>
                    <p class="text-gray-700 text-sm font-semibold">35 Minutes from Your Current Location</p>
                    <p class="text-gray-600 text-md">Contact {{this.gigData.client_name}} NOW</p>
                    <p class="text-gray-500">{{ this.gigData.client_phone_number }}</p>
                </div>
            </div>

            <!-- Customer Address -->
            <div class="bg-white rounded-lg shadow-md border p-4 flex items-start space-x-3">
                <!-- Location Icon -->
                <i class="fas fa-map-marker-alt text-gray-500 text-3xl"></i>
                <div>
                    <p class="text-gray-700 text-sm font-semibold">35 Minutes from Your Current Location</p>
                    <p class="text-gray-600">
                        {{ this.gigData.street_address }} <br />
                        {{ this.gigData.city }}, {{ this.gigData.state }} {{ this.gigData.country }} {{
                        this.gigData.zip_code }}
                    </p>
                </div>
            </div>

            <!-- Samsung Dryer Details -->
            <div class="bg-white rounded-lg shadow-md border p-4 flex items-start space-x-3 cursor-pointer"
                @click="goToModel(this.gigData.model_number)">
                <!-- Appliance Icon -->
                <i class="fas fa-tshirt text-gray-500 text-3xl"></i>
                <div>
                    <p class="text-gray-700 text-sm font-semibold">{{ this.gigData.machine_brand }}</p>
                    <p class="text-gray-600">Model #: {{ this.gigData.model_number }}</p>
                    <p class="text-gray-500">Serial: {{ this.gigData.serial_number }}</p>
                </div>
            </div>


            <!-- Customer Input -->
            <div class="text-center mt-6 border-b pb-5">
                <h3 class="text-lg text-[#232850FF] underline">
                    Customer Input
                </h3>
                <p class="text-sm text-gray-600 mt-2">
                    {{ this.gigData.customer_input }}
                </p>
            </div>

            <!-- Repair Help Section -->
            <div class="mt-6">
                <h3 class="text-lg text-[#232850FF] text-center underline">
                    Repair Help
                </h3>
                <p class="text-sm text-gray-600 text-center">
                    Top 5 Most Common Repairs & Diagnostics <br />
                    #1 Being the most common
                </p>

                <div class="space-y-3 mt-4">
                    <div v-for="repair in numberedRepairs" :key="repair.title"
                        class="bg-white rounded-lg shadow-md border p-4 cursor-pointer"
                        @click="goToRepair(repair.id, this.gigID)">
                        <p class="text-lg font-bold text-gray-700">
                            #{{ repair.number }} {{ repair.title }}
                        </p>
                        <p class="text-gray-700 text-sm"><strong>Symptoms:</strong> {{ repair.symptoms }}</p>
                        <p class="text-gray-500 text-xs"><strong>Solution:</strong> {{ repair.solution }}</p>
                        <p class="text-gray-500 text-xs"><strong>Parts Needed:</strong> {{ repair.parts.join(", ") }}
                        </p>
                    </div>
                </div>

            </div>

            <!-- Easy Repair Video -->
            <div class="mt-6 text-center">
                <h3 class="text-lg text-[#232850FF] underline">
                    Easy Repair Video
                </h3>

                <div class="mt-4 space-y-4">
                    <!-- Placeholder for YouTube Videos -->
                    <div class="bg-gray-200 w-full h-32 flex items-center justify-center rounded-lg">
                        <i class="fas fa-play-circle text-4xl text-gray-500"></i>
                    </div>

                    <div class="bg-gray-200 w-full h-32 flex items-center justify-center rounded-lg">
                        <i class="fas fa-play-circle text-4xl text-gray-500"></i>
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

import Swal from 'sweetalert2'; // Import SweetAlert2

export default {
    components: { NavBar, BottomNav },
    name: "GigPage",
    data() {
        return {
            repairHelp: [],
            count: 1,
            gigData: [],
            machineData: [],
            gigID: null,
            modelNumber: null,
        };
    },
    created() {
        this.gigID = this.$route.params.id;

        if (this.gigID) {
            this.gigDetail(this.gigID);
        }
    },
    watch: {
        // Watch for changes in route (if navigating to another customer)
        '$route.params.id'(gigID) {
            this.gigID = gigID;
            this.gigDetail(this.gigID);
        }
    },
    computed: {
        numberedRepairs() {
            if (!Array.isArray(this.repairHelp)) {
                return []; // Return empty array if it's not an array
            }
            return this.repairHelp.map((repair, index) => ({
                ...repair,
                number: index + 1 // Assigns a sequential number to each item
            }));
        },
        formattedCreatedAt() {
            if (!this.gigData || !this.gigData.start_datetime) return "N/A"; // Handle missing data

            const date = new Date(this.gigData.start_datetime);
            return date.toLocaleDateString("en-US", { month: "long", day: "numeric" });
        },
        formattedCreatedTime() {
            if (!this.gigData || !this.gigData.start_datetime) return "N/A"; // Handle missing data

            const date = new Date(this.gigData.start_datetime);
            return date.toLocaleTimeString("en-US", { hour: "numeric", minute: "2-digit", hour12: true });
        },
        buttonText() {
            if (!this.gigData.time_started) {
                return "Start";
            } else if (this.gigData.time_started && !this.gigData.time_ended) {
                return "End";
            } else {
                return "Submit Report";
            }
        },
        buttonClass() {
            if (!this.gigData.time_started) {
                return "bg-green-600 hover:bg-green-700"; // Green for Start
            } else {
                return "bg-red-600 hover:bg-red-700"; // Red for End and Submit Report
            }
        }
    },
    methods: {
        goToModel(modelID) {
            this.$router.push(`/model/${modelID}`);
        },
        goToRepair(repairId, gigId) {
            this.$router.push(`/gig/${gigId}/repair/${repairId}`);
        },
        goToCustomer(id) {
            this.$router.push(`/customer/${id}`);
        },
        async actionGig(gigID) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                const response = await axios.get(`${api_endpoint}/gigs/startEndGigByID.php?gig_id=${gigID}`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

                Swal.fire({
                    icon: 'success',
                    title: response.data.message,
                    text: 'Success',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Update gigData based on the action taken
                if (response.data.message.includes("started")) {
                    this.gigData.time_started = new Date().toISOString(); // Simulate timestamp
                } else if (response.data.message.includes("ended")) {
                    this.gigData.time_ended = new Date().toISOString(); // Simulate timestamp
                }

            } catch (error) {
                console.error("Error updating gig status:", error);
            }
        },
        async gigDetail(gigID) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                const response = await axios.get(`${api_endpoint}/gigs/retrieveGigByGigID.php?gig_id=${gigID}`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

                this.gigData = response.data.data[0]; 
                this.modelNumber = this.gigData.model_number;
                this. machineDetail(this.modelNumber);

                console.log(this.gigData);

            } catch (error) {
                console.error("Error fetching gig history data:", error);
            }
        },
        async machineDetail(modelNumber) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                const response = await axios.get(`${api_endpoint}/machines/retrieveMachineByID.php?modelNumber=${modelNumber}`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

                this.machineData = response.data.data;
                
                if (this.machineData.common_repairs) {
                    console.log("Raw common_repairs data:", this.machineData.common_repairs); // Log before parsing

                    try {
                        const parsedData = JSON.parse(this.machineData.common_repairs);

                        this.repairHelp = parsedData;

                        console.log("Parsed repairHelp (Array format):", this.repairHelp);
                    } catch (error) {
                        console.error("Error parsing common_repairs JSON:", error);
                        this.repairHelp = [];
                    }
                }
                

                
                console.log('Machine Repairs:', this.repairHelp);
            } catch (error) {
                console.error("Error fetching repair history data:", error);
            }
        }


    }
};
</script>
