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
                <div class="flex items-center space-x-3 mt-2">
                    <img :src="this.machineData.machine_photo" alt="Samsung Dryer" class="w-12 rounded-md" />
                    <div class="text-sm text-gray-600 text-left">
                        <p class="font-semibold">{{ this.capitalizeWords(this.machineData.brand_name) }} - {{
                            this.capitalizeWords(this.machineData.machine_type) }}
                        </p>
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
                <div @click="openGigPotentialEarning()"
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300 cursor-pointer">
                    <span class="text-xl font-bold text-green-500">${{ this.potentialGigPrice }}</span>
                    <p class="text-sm text-gray-500">Gig Potential</p>
                </div>
            </div>

            <!-- Start/End/Submit Report Button -->
            <!-- v-if="!(gigData.time_started && gigData.time_ended)" -->
            <button :class="buttonClass" class="w-full text-white py-2 rounded-lg font-semibold"
                v-if="this.gigData.gig_complete != 3" @click="actionGig(this.gigData.gig_id)">
                {{ buttonText }}
            </button>

            <button v-else :class="buttonClass" class="w-full text-white py-2 rounded-lg font-semibold" type="button"
                disabled>
                Gig Completed & Submitted Report
            </button>

            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-md border p-4 space-y-3">
                <!-- Top Section -->
                <div class="flex justify-between items-center cursor-pointer"
                    @click="goToCustomer(this.gigData.client_id)">
                    <div class="flex items-center space-x-3">
                        <!-- Profile Icon -->
                        <div class="w-12 h-12 flex justify-center items-center">
                            <i class="fas fa-id-card text-gray-500 text-[32px] flex-shrink-0 leading-none"></i>
                        </div>
                        <div>
                            <p class="font-bold text-lg text-gray-900">
                                {{ this.gigData.client_name }} {{ this.gigData.client_last_name }}
                            </p>
                            <p class="text-sm text-gray-500">{{ this.gigData.client_extra_field1 }}</p>
                            <p class="text-xs text-gray-500">{{ this.gigData.client_extra_field2 }}</p>
                        </div>
                    </div>
                    <!-- Star Icon -->
                    <i class="fas fa-star text-yellow-400 text-2xl"
                        v-if="this.gigData.client_total_gig_price > 500"></i>
                </div>

                <!-- Divider -->
                <hr class="border-gray-300" />
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <!-- Dollar Amount -->
                        <p class="text-green-500 font-bold text-lg">${{ this.gigData.client_total_gig_price }}</p>
                    </div>
                    <!-- Lifetime Spend Label -->
                    <label class="text-gray-500 text-left w-full">&nbsp;Lifetime Spend</label>
                    <!-- Toggle Arrow -->
                    <i @click="toggleExpand(index)"
                        class="fas fa-chevron-down text-xl text-gray-500 cursor-pointer transition-transform duration-300"
                        :class="{ 'rotate-180': expandedIndex === index }"></i>
                </div>


                <!-- Expanded Content -->
                <div v-if="expandedIndex === index" class="mt-2 p-2 rounded-md">
                    <!-- <ul v-for="quick in gigQuickHistory" :key="quick.gig_cryptic">
                        <li>
                            <button type="button" @click="goToGig(quick.gig_id)" class="text-gray-500 mt-1">Gig #{{
                                quick.gig_cryptic }} = <span class="text-green-500">${{
                                    quick.gig_price }}</span>
                            </button>
                        </li>
                    </ul> -->

                    <ul>
                        <li>
                            <p>Gigs Spend: ${{ this.gigData.client_total_gig_price }}</p>
                        </li>
                        <li>
                            <p>Insurance Plan: {{ this.gigData.insurance_plan ?? 'N/A' }}</p>
                        </li>
                        <li>
                            <p>Maintenance Plan: {{ this.gigData.maintenance_plan ?? 'N/A' }}</p>
                        </li>
                    </ul>
                </div>

            </div>
            <!-- Customer Location & Contact -->
            <div @click="openModal()"
                class="bg-white rounded-lg shadow-md border p-4 flex items-center space-x-3 cursor-pointer">
                <!-- Phone Icon -->
                <div class="w-12 h-12 flex justify-center items-center">
                    <i class="fas fa-phone-alt text-gray-500 text-[32px] flex-shrink-0 leading-none"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-md">Contact {{ this.gigData.client_name }}</p>
                    <p class="text-gray-500">Message or Call {{ this.gigData.client_name }}</p>
                    <a href="javascript:void(0);" class="text-gray-500">
                        {{ gigData.client_phone_number }}
                    </a>
                </div>
            </div>

            <!-- Customer Address -->
            <div @click="openGoogleMaps"
                class="bg-white rounded-lg shadow-md border p-4 flex items-center space-x-3 cursor-pointer">
                <!-- Location Icon -->
                <div class="w-12 h-12 flex justify-center items-center">
                    <i class="fas fa-map-marker-alt text-gray-500 text-[32px] flex-shrink-0 leading-none"></i>
                </div>
                <div>
                    <p class="text-gray-700 text-sm font-semibold">35 Minutes from Your Current Location</p>
                    <p class="text-gray-600">
                        {{ gigData.street_address }} {{ gigData.city }}, {{ gigData.state }} {{ gigData.country }} {{
                        gigData.zip_code }}
                    </p>
                </div>
            </div>

            <!-- Samsung Dryer Details -->
            <div class="bg-white rounded-lg shadow-md border p-4 flex items-center space-x-3 cursor-pointer"
                @click="goToModel()">
                <!-- Appliance Image -->
                <img :src="`/images/machine/${this.machineData.machine_type}.png`"
                    :alt="`${this.machineData.machine_type} Image`" class="w-12 h-12 object-contain self-center">
                <div>
                    <p class="text-gray-700 text-sm font-semibold">{{ this.capitalizeWords(this.machineData.brand_name)
                        }}</p>
                    <p class="text-gray-600">Model #: {{ this.machineData.model_number }}</p>
                    <p class="text-gray-500">Serial: {{ this.gigData.serial_number }}</p>
                </div>
            </div>

            <!-- Customer Input -->
            <div class="text-center mt-6 border-b pb-5">
                <h3 class="text-lg text-[#232850FF] underline">
                    Customer Input
                </h3>
                <p class="text-sm text-gray-600 mt-2">
                    {{ this.gigData.initial_issue }}
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
                    <div v-for="repair in numberedRepairs" :key="repair.repairName"
                        class="bg-white rounded-lg shadow-md border p-4 cursor-pointer"
                        @click="goToRepair(repair.id, this.gigID)">
                        <p class="text-lg font-bold text-gray-700">
                            #{{ repair.number }} {{ repair.repairName }}
                        </p>
                        <p class="text-gray-700 text-sm"><strong>Symptoms:&nbsp;</strong> {{ repair.symptoms }}</p>
                        <p class="text-gray-500 text-xs mt-1"><strong>Solution:&nbsp;</strong>
                            <br>
                            <span v-html="repair.solution.replace(/\n/g, '<br/>')"></span>
                        </p>
                        <p class="text-gray-500 text-xs mt-1"><strong>Parts Needed:&nbsp;</strong> {{
                            repair.partsNeeded.join(", ")
                            }}
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

                    <!-- <div class="bg-gray-200 w-full h-32 flex items-center justify-center rounded-lg">
                        <i class="fas fa-play-circle text-4xl text-gray-500"></i>
                    </div> -->
                    <!-- Loop through each video URL -->
                    <div v-for="(videoLink, index) in repairVideo" :key="index"
                        class="bg-gray-200 w-full h-72 flex items-center justify-center rounded-lg">
                        <iframe :src="transformToEmbedUrl(videoLink)" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen class="w-full h-72 rounded-lg"></iframe>
                    </div>
                </div>
            </div>




        </div>

        <BottomNav />
    </div>



    <!-- Mobile Responsive Modal -->
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4"
        @click="closeModal()">
        <div class="bg-white p-6 w-full max-w-md mx-auto rounded-lg" @click.stop>
            <h2 class="text-lg font-bold text-center mb-4">Select an Action</h2>

            <!-- Call Now Button -->
            <a :href="`tel:${gigData.client_phone_number}`" class="card flex items-center p-3 border rounded-lg mt-3">
                <i class="fas fa-phone text-gray-500 text-3xl"></i>
                <div class="ml-4">
                    <p class="font-medium">Call Now</p>
                    <label class="text-gray-500">
                        {{ gigData.client_phone_number }}
                    </label>
                </div>
            </a>

            <!-- Arriving Early Message -->
            <div class="card flex items-center p-3 border rounded-lg mt-3 cursor-pointer"
                @click="sendMessage('arriving-early')" :class="{ 'opacity-50 pointer-events-none': isSending }">
                <i class="fa-regular fa-message text-gray-500 text-3xl"></i>
                <div class="ml-4">
                    <p class="font-medium">Message</p>
                    <p class="text-blue-500">Arriving Early</p>
                </div>
            </div>

            <!-- On Time Message -->
            <div class="card flex items-center p-3 border rounded-lg mt-3 cursor-pointer"
                @click="sendMessage('on-time')" :class="{ 'opacity-50 pointer-events-none': isSending }">
                <i class="fa-regular fa-message text-gray-500 text-3xl"></i>
                <div class="ml-4">
                    <p class="font-medium">Message</p>
                    <p class="text-green-500">On Time</p>
                </div>
            </div>

            <!-- Behind Schedule Message -->
            <div class="card flex items-center p-3 border rounded-lg mt-3 cursor-pointer"
                @click="sendMessage('behind-schedule')" :class="{ 'opacity-50 pointer-events-none': isSending }">
                <i class="fa-regular fa-message text-gray-500 text-3xl"></i>
                <div class="ml-4">
                    <p class="font-medium">Message</p>
                    <p class="text-red-500">Behind Schedule</p>
                </div>
            </div>

            <!-- Close Button -->
            <button @click="closeModal()" class="mt-4 w-full px-4 py-2 bg-gray-700 text-white rounded-lg">
                Close
            </button>
        </div>
    </div>

    <!-- Modal -->
    <div v-if="isGigPotentialOpen" class="fixed inset-0 flex items-center justify-center z-50">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black opacity-50" @click="isGigPotentialOpen = false"></div>
        <!-- Modal content -->
        <div class="bg-white p-6 rounded-md shadow-lg relative z-10 max-w-xs w-full">
            <h3 class="text-lg mb-2">Gig Potential</h3>
            <ul v-for="earning in gigPotentialEarnings" :key="earning.name">
                <li>{{ earning.name }} = <span class="text-green-500">${{ earning.amount }}</span></li>
            </ul>
            <button @click="isGigPotentialOpen = false" class="mt-4 px-4 py-2 bg-[#232850FF] text-white rounded">
                Close
            </button>
        </div>
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
            repairVideo: [],
            repairHelp: [],
            count: 1,
            gigData: [],
            machineData: [],
            gigID: null,
            modelNumber: null,
            expandedIndex: null,
            gigQuickHistory: [],
            isOpen: false,
            isSending: false, 
            gigPotentialEarnings: [],
            potentialGigPrice: 0.00,
            isGigPotentialOpen: false
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
            return new Intl.DateTimeFormat("en-US", { month: "long", day: "numeric", timeZone: "UTC" }).format(date);
        },
        formattedCreatedTime() {
            if (!this.gigData || !this.gigData.start_datetime) return "N/A"; // Handle missing data

            const date = new Date(this.gigData.start_datetime);
            return new Intl.DateTimeFormat("en-US", {
                hour: "numeric",
                minute: "2-digit",
                hour12: true,
                timeZone: "UTC"
            }).format(date);
        },
        buttonText() {

            console.log(`gig_complete = ${this.gigData.gig_complete}`);

            if (this.gigData.gig_complete == 0) {
                return "Start";
            } else if (this.gigData.gig_complete == 1) {
                return "End";
            } else if (this.gigData.gig_complete == 2) {
                return "Complete Post Gig Report & Get Paid";
            } else if (this.gigData.gig_complete == 3) {
                return "Submitted Post Gig Report";
            }
        },
        buttonClass() {
            if (this.gigData.gig_complete == 0) {
                return "bg-green-600 hover:bg-green-700";
            } else if (this.gigData.gig_complete == 1) {
                return "bg-red-600 hover:bg-red-700";
            } else if (this.gigData.gig_complete == 2) {
                return "bg-green-600 hover:bg-green-700";
            } else if (this.gigData.gig_complete == 3) {
                return "bg-gray-600 hover:bg-gray-700";
            }


        }
    },
    methods: {
        toggleExpand(index) {
            this.expandedIndex = this.expandedIndex === index ? null : index;
        },
        capitalizeWords(str) {
            if (!str) return ''; // Return an empty string if str is undefined/null
            return str.replace(/\b\w/g, char => char.toUpperCase());
        },
        goToModel() {
            this.$router.push(`/model/${this.gigData.model_number}/gig/${this.gigData.gig_id}`);
        },
        goToRepair(repairId, gigId) {
            this.$router.push(`/gig/${gigId}/repair/${repairId}`);
        },
        goToCustomer(id) {
            this.$router.push(`/customer/${id}/gig/${this.gigData.gig_id}`);
        },
        goToGig(gigID) {
            this.$router.push(`/gig/${gigID}`);
        },
        async actionGig(gigID) {

            if (this.gigData.time_started && this.gigData.time_ended) {
                this.$router.push(`/gig-report/${gigID}`);
                return;
            }

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
                    this.gigData.time_started = new Date().toISOString();
                    this.gigData.gig_complete = 1; // Update status
                } else if (response.data.message.includes("ended")) {
                    this.gigData.time_ended = new Date().toISOString();
                    this.gigData.gig_complete = 2; // Update status

                    if (this.gigData.time_started && this.gigData.time_ended) {
                        this.$router.push(`/gig/${gigID}`);
                        return;
                    }
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
                console.log(`gig -> ${this.gigData.model_number}`);
                this.machineDetail(this.modelNumber);
                this.getQuickGigHistory(this.gigData.client_id);
                console.log(`Gig Data: ` , this.gigData );

                // if (this.gigData.top_recommended_repairs) {
                //     try {
                //         const parsedData = JSON.parse(this.gigData.top_recommended_repairs);

                //         this.repairHelp = parsedData;
                //     } catch (error) {
                //         this.repairHelp = [];
                //     }
                // }


                if (this.gigData.top_recommended_repairs) {
                    try {
                        const parsedData = JSON.parse(this.gigData.top_recommended_repairs);
                        this.repairHelp = parsedData;
                        this.repairVideo = []; // Initialize the array

                        console.log(`videos: `, parsedData);

                        // // Loop through each repair object
                        parsedData.forEach(repair => {
                            // Check if youtubeLinks exists and is an array
                            if (repair.youtubeLinks && Array.isArray(repair.youtubeLinks)) {
                                // Loop through each link and add it to repairVideo
                                repair.youtubeLinks.forEach(link => {
                                    this.repairVideo.push(link);
                                });
                            }
                        });
                    } catch (error) {
                        this.repairHelp = [];
                        this.repairVideo = [];
                    }
                }



                // Determine upsell availability: upsell is available if the client is missing one or both plans.
                const hasUpsellPotential = !(this.gigData.insurance_plan && this.gigData.maintenance_plan);

                // Determine the basic rate based on technician rank
                const basic_rate = this.gigData.tech_rank_type === "0" ? 40.00 : 50.00;

                // Empty the earnings array
                this.gigPotentialEarnings = [];

                // First-time visit logic
                if (this.gigData.gig_type == "0") {
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
                else if (this.gigData.gig_type == "1") {
                    // Return repair scenario
                    this.gigPotentialEarnings.push({ name: "Return repair", amount: basic_rate });
                    // Return repair with upsell scenario (if upsell is applicable)
                    if (hasUpsellPotential) {
                        this.gigPotentialEarnings.push({ name: "Return repair + upsell", amount: basic_rate + 25.00 });
                    }
                }

                // Calculate the potential gig price by summing the amounts of all scenarios
                // const potentialGigPrice = this.gigPotentialEarnings.reduce((total, item) => total + item.amount, 0);
                if (this.gigPotentialEarnings.length > 0) {
                    // Option 1: Using Math.max and Array.map
                    this.potentialGigPrice = Math.max(...this.gigPotentialEarnings.map(item => item.amount));

                    // Option 2: Alternatively using Array.reduce:
                    // this.potentialGigPrice = this.gigPotentialEarnings.reduce(
                    //   (max, item) => (item.amount > max ? item.amount : max), 0
                    // );
                } else {
                    this.potentialGigPrice = 0;
                }


                console.log(`Total Client Price`, response);

            } catch (error) {
                console.error("Error fetching gig history data:", error);
            }
        },
        async machineDetail(modelNumber) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;
                console.log(modelNumber);
                const response = await axios.get(`${api_endpoint}/machines/retrieveMachineByID.php?modelNumber=${modelNumber}`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

                this.machineData = response.data.data;
                
                // if (this.machineData.common_repairs) {
                //     console.log("Raw common_repairs data:", this.machineData.common_repairs); // Log before parsing

                //     try {
                //         const parsedData = JSON.parse(this.machineData.common_repairs);

                //         this.repairHelp = parsedData;

                //         console.log("Parsed repairHelp (Array format):", this.repairHelp);
                //     } catch (error) {
                //         console.error("Error parsing common_repairs JSON:", error);
                //         this.repairHelp = [];
                //     }
                // }
                

                console.log(`response machine data: `, response);
                console.log('Machine Repairs:', this.repairHelp);
            } catch (error) {
                console.error("Error fetching repair history data:", error);
            }
        },
        async getQuickGigHistory(clientId) {
            const api_endpoint = import.meta.env.VITE_API_ENDPOINT_MAIN;
            const token = localStorage.getItem("token");
            const response = await axios.get(`${api_endpoint}/api/gig/quick/history/${clientId}`, {
                headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
            });
            this.gigQuickHistory = response.data.data;
            console.log(`quick: `, response);
        },
        openModal() {
            this.isOpen = true;
        },
        closeModal() {
            this.isOpen = false;
        },
        async sendMessage(type) {

            this.isSending = true;

            let eventTime = new Date(this.gigData.start_datetime);
            if (isNaN(eventTime)) {
                console.error("Invalid event start time:", this.gigData.start_datetime);
                alert("Invalid date format. Please check the event time.");
                this.isSending = false;
                return;
            }

            const formattedTime = eventTime.toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
                hour12: true,
            });

            const messages = {
                "arriving-early": [
                    `Hi ${this.gigData.client_name}, it’s ${this.gigData.tech_name} with Appliance Repair American. I’m running a bit ahead of schedule and could arrive earlier than ${formattedTime}. Would that be okay?`,
                    `Hello ${this.gigData.client_name}! This is ${this.gigData.tech_name}. I’m ahead of schedule and can get to you earlier than planned. Let me know if that works for you.`,
                    `Greetings ${this.gigData.client_name}, this is ${this.gigData.tech_name} with Appliance Repair American. I’m running early—would you be available before ${formattedTime}?`,
                ],
                "on-time": [
                    `Hello ${this.gigData.client_name} 🌞. This is ${this.gigData.tech_name} with Appliance Repair American. I am on time to see you at ${formattedTime}. Does this still work for you?`,
                    `Hi ${this.gigData.client_name}! ${this.gigData.tech_name} from Appliance Repair American here. I’m scheduled to arrive at ${formattedTime}. Just checking if we’re still good for that time.`,
                    `Good day ${this.gigData.client_name}, this is ${this.gigData.tech_name} from Appliance Repair American. I’ll be arriving at ${formattedTime} as planned. Is that still okay with you?`,
                ],
                "behind-schedule": [
                    `Hi ${this.gigData.client_name}, this is ${this.gigData.tech_name} with Appliance Repair American. I’m running behind some and will be arriving later than scheduled. Sorry for the delay.`,
                    `Hello ${this.gigData.client_name}, just a quick update — I’m arriving later than expected. Sorry for the inconvenience, and I appreciate your patience.`,
                    `Hey ${this.gigData.client_name}, this is ${this.gigData.tech_name}. I’m running a bit behind and will be there later than originally planned. Sorry for the change.`,
                ],
            };

            const selectedMessage =
                messages[type][Math.floor(Math.random() * messages[type].length)];

            const smsLink = `sms:${this.gigData.client_phone_number}?&body=${encodeURIComponent(
                selectedMessage
            )}`;

            window.location.href = smsLink;


            this.isSending = false;


            // Disabled TWILIO as per Jacob
            // if (this.isSending) {
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Invalid duplicate click',
            //         text: 'You cannot send SMS Twice at the same time',
            //         timer: 2000,
            //         showConfirmButton: false
            //     });
            // }

            // this.isSending = true; // Set loading state to true

            // try {
            //     const api_endpoint = import.meta.env.VITE_API_ENDPOINT_MAIN;
            //     const token = localStorage.getItem("token");


            //     const formData = new FormData();
            //     formData.append("type", type);
            //     formData.append("gig_id", this.gigData.gig_id);

            //     const response = await axios.post(`${api_endpoint}/api/gig/send_sms/contact`, formData, {
            //         headers: {
            //             Authorization: `Bearer ${token}`,
            //             "Content-Type": "multipart/form-data",
            //         },
            //     });

            //     console.log(response);

            //     Swal.fire({
            //         icon: 'success',
            //         title: 'SMS Sent!',
            //         text: 'SMS message successfully sent!',
            //         timer: 2000,
            //         showConfirmButton: false
            //     });
            // } catch (error) {
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Error',
            //         text: error,
            //         timer: 2000,
            //         showConfirmButton: false
            //     });
            // } finally {
            //     this.isSending = false; // Reset flag after request is done
            // }

        },
        openGoogleMaps() {
            const address = `${this.gigData.street_address}, ${this.gigData.city}, ${this.gigData.state}, ${this.gigData.country} ${this.gigData.zip_code}`;
            const encodedAddress = encodeURIComponent(address);
            window.open(`https://www.google.com/maps/search/?api=1&query=${encodedAddress}`, "_blank");
        },
        transformToEmbedUrl(youtubeUrl) {
            return `https://www.youtube.com/embed/${youtubeUrl.videoId}`;
        },
        openGigPotentialEarning() {
            this.isGigPotentialOpen = true;
        }
    }
};
</script>


<style scoped>
.modal-overlay {
    @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center;
}

.modal {
    @apply bg-white p-6 rounded-lg shadow-lg;
}

.card {
    @apply flex items-center gap-4 p-4 bg-white rounded-lg shadow-md cursor-pointer hover:bg-gray-100;
}

.icon {
    @apply w-6 h-6 text-gray-500;
}
</style>