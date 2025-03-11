<template>
    <div class="mt-20 mb-20">
        <NavBar />
        <!-- Customer Profile -->
        <div class="max-w-lg mx-auto p-6 text-center">
            <h2 class="text-2xl text-[#232850]">{{ customerData.client_name }}</h2>
            <p class="text-sm text-gray-600">Gold Star Customer</p>
            <p class="text-xs text-gray-500">**Maintenance Plan Member**</p>
            <p class="text-xs text-gray-500">*ARA Insured*</p>
            <i class="fas fa-star text-yellow-500 text-2xl mt-2"></i>
        </div>

        <!-- DAX & Lifetime Spend -->
        <div class="max-w-lg mx-auto grid grid-cols-2 gap-4 p-6">


            <div
                class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 text-center 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                <i class="fas fa-headset text-2xl text-gray-700"></i>
                <p class="text-sm font-medium ml-2">DAX</p>
            </div>
            <div
                class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 text-center 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                <p class="text-lg font-bold text-green-600">$750</p>
                <p class="text-sm text-gray-500">Lifetime Spend</p>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="max-w-lg mx-auto space-y-4 p-6">
            <div class="bg-white shadow-md rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-phone text-xl text-[#666666FF]"></i>
                    <div>
                        <p class="text-sm font-medium text-[#666666FF]">Contact {{ customerData.client_name }} NOW</p>
                        <p class="text-xs text-gray-500">{{ customerData.phone_number }}</p>
                    </div>
                </div>
                <!-- <i class="fas fa-edit text-gray-400"></i> -->
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-id-card text-xl text-[#666666FF]"></i>
                    <div>
                        <p class="text-sm font-medium text-[#666666FF]">EMAIL</p>
                        <p class="text-xs text-gray-500">{{ customerData.email }}</p>
                    </div>
                </div>
                <!-- <i class="fas fa-edit text-gray-400"></i> -->
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-map-marker-alt text-xl text-[#666666FF]"></i>
                    <div>
                        <p class="text-sm font-medium text-[#666666FF]">
                            35 Minutes from Your Location
                        </p>
                        <p class="text-xs text-[#666666FF]">
                            {{ customerData.street_address }} {{ customerData.state }} {{ customerData.zip_code }} {{
                            customerData.country }}
                        </p>
                    </div>
                </div>
                <!-- <i class="fas fa-edit text-gray-400"></i> -->
            </div>
            <div class="bg-white shadow-md rounded-lg p-4">
                <p class="text-sm font-medium text-[#666666FF]">{{ customerData.extra_field1 }}</p>
                <!-- <p class="text-xs text-gray-500">Gate Code #1975</p> -->
                <p class="text-xs text-gray-500">{{ customerData.extra_field2 }}</p>
            </div>
        </div>


        <!-- Gig History -->
        <div class="max-w-lg mx-auto p-6">
            <h3 class="text-lg text-[#666666FF] text-center font-medium">Gig History</h3>
            <div class="space-y-4 mt-3">
                <div v-if="customerData.gigs && customerData.gigs.length" class="space-y-4">
                    <div v-for="gig in customerData.gigs" :key="gig.gig_id"
                        class="bg-white shadow-md rounded-lg p-4 cursor-pointer" @click="goToGig(gig.gig_id)">
                        <p class="text-sm font-medium text-[#666666FF]">Gig# {{ gig.gig_cryptic || `ID-${gig.gig_id}` }}
                        </p>
                        <p class="text-xs text-[#666666FF]">Date: {{ formatDate(gig.created_at) }}</p>
                        <p v-if="gig.serial_number" class="text-xs text-[#666666FF]">Serial: {{ gig.serial_number }}</p>
                        <p class="text-xs text-[#666666FF]">{{ gig.repair_notes || "No repair notes available" }}</p>
                    </div>
                </div>
                <div v-else>
                    <p class="text-center text-gray-500">No gig history available.</p>
                </div>
            </div>
        </div>


        <!-- Appliance Models -->
        <div class="max-w-lg mx-auto p-6">
            <h3 class="text-lg text-[#666666FF] text-center font-medium">Appliance Models</h3>
            <div class="space-y-4 mt-3">
                <div v-if="customerData.machines && customerData.machines.length" class="space-y-4">
                    <div v-for="machine in customerData.machines" :key="machine.machine_id"
                        class="bg-white shadow-md rounded-lg p-4 cursor-pointer" @click="goToModel(machine.machine_id)">
                        <p class="text-sm font-medium text-[#666666FF]">{{ machine.brand_name }} {{ machine.machine_type
                            }}</p>
                        <p class="text-xs text-gray-500">Model #: {{ machine.model_number }}</p>
                        <p class="text-xs text-gray-500">Serial: {{ machine.serial_number }}</p>
                        <!-- <p class="text-xs text-gray-500">
                            <a :href="machine.machine_photo" target="_blank" class="text-blue-500 underline">View
                                Photo</a>
                        </p> -->
                    </div>
                </div>
                <div v-else>
                    <p class="text-center text-gray-500">No appliances available.</p>
                </div>
            </div>
        </div>


        <BottomNav />
    </div>
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    components: { NavBar, BottomNav },
    name: "CustomerPage",
    data() {
        return {
            customerId: null,  // Store the dynamic customer ID
            customerData: {},
            loading: true
        };
    },
    created() {
        this.customerId = this.$route.params.id;
        console.log("Extracted Customer ID:", this.customerId); // Debugging

        if (this.customerId) {
            this.clientData(this.customerId);
        }
    },
    watch: {
        // Watch for changes in route (if navigating to another customer)
        '$route.params.id'(newId) {
            this.customerId = newId;
            this.clientData(newId);
        }
    },
    methods: {
        formatDate(dateStr) {
            if (!dateStr) return "N/A";
            return new Date(dateStr).toLocaleDateString("en-US", { month: "short", day: "2-digit", year: "numeric" });
        },
        goToModel(id) {
            this.$router.push(`/model/${id}`);
        },
        goToGig(id) {
            this.$router.push(`/gig/${id}`);
        },
        async clientData(id) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                console.log("Fetching data for ID:", id);

                const response = await axios.post(`${api_endpoint}/clients/retrieveClient.php`,
                    {
                        client_id: id
                    },
                    {
                        headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                    }
                );
                console.log(response);

                this.customerData = response.data.data;
                this.loading = false; // Turn off loading

                console.log(response);

            } catch (error) {
                console.error("Error fetching customer data:", error);
                Swal.fire("Error", "Failed to load customer data.", "error");
                this.loading = false; // Turn off loading even on error
            }
        }


    }
};
</script>