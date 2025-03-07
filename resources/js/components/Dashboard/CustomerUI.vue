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
                <i class="fas fa-edit text-gray-400"></i>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-id-card text-xl text-[#666666FF]"></i>
                    <div>
                        <p class="text-sm font-medium text-[#666666FF]">EMAIL</p>
                        <p class="text-xs text-gray-500">{{ customerData.email }}</p>
                    </div>
                </div>
                <i class="fas fa-edit text-gray-400"></i>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-map-marker-alt text-xl text-[#666666FF]"></i>
                    <div>
                        <p class="text-sm font-medium text-[#666666FF]">
                            35 Minutes from Your Location
                        </p>
                        <p class="text-xs text-[#666666FF]">
                            {{ customerData.street_address }} {{ customerData.state }} {{ customerData.zip_code }} {{ customerData.country }}
                        </p>
                    </div>
                </div>
                <i class="fas fa-edit text-gray-400"></i>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4">
                <p class="text-sm font-medium text-[#666666FF]">Dog's Name is "Biscuit"</p>
                <p class="text-xs text-gray-500">Gate Code #1975</p>
                <p class="text-xs text-gray-500">Husband's name "Fred"</p>
            </div>
        </div>

        <!-- Gig History -->
        <div class="max-w-lg mx-auto p-6">
            <h3 class="text-lg text-[#666666FF] text-center font-medium">Gig History</h3>
            <div class="space-y-4 mt-3">
                <div class="bg-white shadow-md rounded-lg p-4">
                    <p class="text-sm font-medium text-[#666666FF]">Gig# R-LDNH307</p>
                    <p class="text-xs text-[#666666FF]">Date: 11/23/23</p>
                    <p class="text-xs text-[#666666FF]">
                        Repaired Samsung Dryer no Heat
                    </p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <p class="text-sm font-medium text-[#666666FF]">Gig# R-LWNS345</p>
                    <p class="text-xs text-[#666666FF]">Date: 07/20/23</p>
                    <p class="text-xs text-[#666666FF]">Serial: SN1600005756923</p>
                </div>
            </div>
        </div>

        <!-- Appliance Models -->
        <div class="max-w-lg mx-auto p-6">
            <h3 class="text-lg text-[#666666FF] text-center font-medium">Appliance Models</h3>
            <div class="space-y-4 mt-3">
                <div class="bg-white shadow-md rounded-lg p-4">
                    <p class="text-sm font-medium text-[#666666FF]">Samsung Dryer</p>
                    <p class="text-xs text-gray-500">
                        Model #: WDW9500SS/AA-001
                    </p>
                    <p class="text-xs text-gray-500">Serial: SN1600005756923</p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <p class="text-sm font-medium text-[#666666FF]">LG Refrigerator</p>
                    <p class="text-xs text-gray-500">
                        Model #: WDW9500SS/AA-001
                    </p>
                    <p class="text-xs text-gray-500">Serial: SN1600005756923</p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <p class="text-sm font-medium text-[#666666FF]">Samsung Washer</p>
                    <p class="text-xs text-gray-500">
                        Model #: WDW9500SS/AA-001
                    </p>
                    <p class="text-xs text-gray-500">Serial: SN1600005756923</p>
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
        async clientData(id) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                console.log("Fetching data for ID:", id);

                const response = await axios.get(`${api_endpoint}/clients/retrieveClient.php`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

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