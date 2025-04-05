<template>
    <div class="mt-20 mb-20">
        <NavBar />
        <!-- Customer Profile -->
        <div class="max-w-lg mx-auto p-6 text-center">
            <h2 class="text-2xl text-[#232850]">
                {{ customerData.client_name }} {{ customerData.client_last_name }}
            </h2>
            <p class="text-sm text-gray-600" v-if="this.customerData.client_total_gig_price > 500">
                Gold Star Customer
            </p>
            <p class="text-xs text-gray-500">**NOT Maintenance Plan Member**</p>
            <p class="text-xs text-gray-500">*NOT ARA Insured*</p>
            <i class="fas fa-star text-yellow-500 text-2xl mt-2"
                v-if="this.customerData.client_total_gig_price > 500"></i>
        </div>

        <!-- DAX & Lifetime Spend -->
        <div class="max-w-lg mx-auto grid grid-cols-2 gap-4 p-6">
            <div
                class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 text-center transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                <i class="fas fa-headset text-2xl text-gray-700"></i>
                <p class="text-sm font-medium ml-2">DAX</p>
            </div>
            <div
                class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 text-center transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                <p class="text-lg font-bold text-green-600">
                    ${{ customerData.client_total_gig_price }}
                </p>
                <p class="text-sm text-gray-500">Lifetime Spend</p>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="max-w-lg mx-auto space-y-4 p-6">
            <!-- <a :href="'tel:' + customerData.phone_number"
                 class="bg-white shadow-md rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 flex justify-center items-center">
                        <i class="fas fa-phone text-gray-500 text-[32px] flex-shrink-0 leading-none"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[#666666FF]">
                            Contact {{ customerData.client_name }} {{ customerData.client_last_name }} NOW
                        </p>
                        <div class="text-xs text-gray-500">
                            {{ customerData.phone_number }}
                        </div>
                    </div>
                </div>
            </a> -->

            <!-- Customer Location & Contact -->
            <div @click="openModal()"
                class="bg-white rounded-lg shadow-md border p-4 flex items-center space-x-3 cursor-pointer">
                <!-- Phone Icon -->
                <div class="w-12 h-12 flex justify-center items-center">
                    <i class="fas fa-phone-alt text-gray-500 text-[32px] flex-shrink-0 leading-none"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-md">Contact {{ customerData.client_name }} NOW</p>
                    <a href="javascript:void(0);" class="text-gray-500">
                        {{ customerData.phone_number }}
                    </a>
                </div>
            </div>



            <a :href="'mailto:' + customerData.email"
                class="bg-white shadow-md rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <!-- <i class="fas fa-id-card text-xl text-[#666666FF]"></i> -->
                    <div class="w-12 h-12 flex justify-center items-center">
                        <i class="fas fa-id-card text-gray-500 text-[32px] flex-shrink-0 leading-none"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[#666666FF]">
                            EMAIL
                        </p>
                        <div class="text-xs text-gray-500">
                            {{ customerData.email }}
                        </div>
                    </div>
                </div>
                <!-- <i class="fas fa-edit text-gray-400"></i> -->
            </a>
            <a :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(
                customerData.street_address +
                ', ' +
                customerData.city +
                ', ' +
                customerData.state +
                ', ' +
                customerData.country +
                ' ' +
                customerData.zip_code
            )}`" class="bg-white shadow-md rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <!-- <i class="fas fa-map-marker-alt text-xl text-[#666666FF]"></i> -->
                    <div class="w-12 h-12 flex justify-center items-center">
                        <i class="fas fa-map-marker-alt text-gray-500 text-[32px] flex-shrink-0 leading-none"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[#666666FF]">
                            35 Minutes from Your Location
                        </p>
                        <div class="text-xs text-[#666666FF]" target="_blank">
                            {{ customerData.street_address }}
                            {{ customerData.city }} {{ customerData.state }}
                            {{ customerData.zip_code }}
                            {{ customerData.country }}
                        </div>
                    </div>
                </div>
                <!-- <i class="fas fa-edit text-gray-400"></i> -->
            </a>
            <div class="bg-white shadow-md rounded-lg p-4">
                <p class="text-sm font-medium text-[#666666FF]">
                    {{ customerData.extra_field1 }}
                </p>
                <!-- <p class="text-xs text-gray-500">Gate Code #1975</p> -->
                <p class="text-xs text-gray-500">
                    {{ customerData.extra_field2 }}
                </p>
            </div>
        </div>

        <!-- Gig History -->
        <div class="max-w-lg mx-auto p-6">
            <h3 class="text-lg text-[#666666FF] text-center font-medium">
                Gig History
            </h3>
            <div class="space-y-4 mt-3">
                <div v-if="customerData.gigs && customerData.gigs.length" class="space-y-4">

                    <div v-for="gig in customerData.gigs" :key="gig.gig_id"
                        class="bg-white shadow-md rounded-lg p-4 cursor-pointer" @click="goToGig(gig.gig_id)">


                        <div class="flex items-center space-x-3">
                            <!-- <i class="fas fa-map-marker-alt text-xl text-[#666666FF]"></i> -->
                            <div class="w-12 h-12 flex justify-center items-center">
                                <i class="fa-solid fa-folder-open text-gray-500 text-[32px] flex-shrink-0
                                    leading-none"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-[#666666FF]">
                                    Gig#
                                    {{
                                    this.capitalizeWords(gig.gig_cryptic) ||
                                    `ID-${gig.gig_id}`
                                    }}
                                </p>
                                <p class="text-xs text-[#666666FF]">
                                    Date: {{ formatDateTime(gig.start_datetime) }}
                                </p>
                                <p v-if="gig.serial_number" class="text-xs text-[#666666FF]">
                                    Serial: {{ gig.serial_number }}
                                </p>
                                <p class="text-xs text-[#666666FF]">
                                    {{
                                    gig.initial_issue || "No repair notes available"
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                <div v-else>
                    <p class="text-center text-gray-500">
                        No gig history available.
                    </p>
                </div>
            </div>
        </div>

        <!-- Appliance Models -->
        <div class="max-w-lg mx-auto p-6">
            <h3 class="text-lg text-[#666666FF] text-center font-medium">
                Appliance Models
            </h3>
            <div class="space-y-4 mt-3">
                <div v-if="customerData.machines && customerData.machines.length" class="space-y-4">
                    <div v-for="machine in customerData.machines" :key="machine.machine_id"
                        class="bg-white shadow-md rounded-lg p-4 cursor-pointer flex items-center"
                        @click="goToModel(machine.model_number)">
                        <!-- Image displayed on the left -->
                        <img v-if="machine.machine_photo" :src="machine.machine_photo" alt="Machine Photo"
                            class="w-12 h-full object-cover rounded mr-4">
                        <!-- Machine details on the right -->
                        <div>
                            <p class="text-sm font-medium text-[#666666FF]">
                                {{ this.capitalizeWords(machine.brand_name) }}
                                {{ this.capitalizeWords(machine.machine_type) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Model #: {{ machine.model_number }}
                            </p>
                            <p v-if="machine.extra_field1" class="text-xs text-gray-500">
                                {{ machine.extra_field1 }}
                            </p>
                            <p v-if="machine.extra_field2" class="text-xs text-gray-500">
                                {{ machine.extra_field2 }}
                            </p>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <p class="text-center text-gray-500">
                        No appliances available.
                    </p>
                </div>
            </div>
        </div>

        <BottomNav />
    </div>

    <!-- Modal -->
    <div v-if="isOpen" class="modal-overlay">
        <div class="modal" @click.stop>
            <!-- Image & Buttons -->
            <div class="bg-white p-4 w-96">
                <!-- Buttons as Containers -->
                <div class="card mt-3">

                    <i class="fas fa-phone text-gray-500 text-3xl"></i>
                    <div>
                        <p class="font-medium">Call Now</p>
                        <a :href="`sms:${customerData.phone_number}`" class="text-gray-500"> {{
                            customerData.phone_number }}</a>
                    </div>
                </div>
                <!-- Close Button -->
                <button @click="closeModal()" class="mt-4 px-4 py-2 bg-gray-700 text-white rounded-lg w-full">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import axios from "axios";
import Swal from "sweetalert2";

export default {
    components: { NavBar, BottomNav },
    name: "CustomerPage",
    data() {
        return {
            customerId: null, // Store the dynamic customer ID
            customerData: {},
            loading: true,
            isOpen: false,
            gigId: null,
        };
    },
    created() {
        this.customerId = this.$route.params.id;
        this.gigId = this.$route.params.gigId;
        console.log("Extracted Customer ID:", this.customerId); // Debugging

        if (this.customerId) {
            this.clientData(this.customerId);
        }
    },
    watch: {
        // Watch for changes in route (if navigating to another customer)
        "$route.params.id"(newId) {
            this.customerId = newId;
            this.clientData(newId);
        },
        "$route.params.gigId"(gigId) {

            this.gigId = gigId;
        }
    },
    methods: {
        capitalizeWords(str) {
            if (!str) return ""; // Return an empty string if str is undefined/null
            return str.replace(/\b\w/g, (char) => char.toUpperCase());
        },
        formatDateTime(dateStr) {
            if (!dateStr) return "N/A";
            // Ensure the date string is in ISO format with a 'Z' to indicate UTC
            const utcDateStr = dateStr.includes("T")
                ? dateStr
                : dateStr.replace(" ", "T") + "Z";
            return new Date(utcDateStr).toLocaleString("en-US", {
                timeZone: "UTC",
                month: "short",
                day: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
                hour12: true,
            });
        },
        goToModel(id) {
            this.$router.push(`/model/${id}/gig/${this.gigId}`);
        },
        goToGig(id) {
            this.$router.push(`/gig/${id}`);
        },
        async clientData(id) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                console.log("Fetching data for ID:", id);

                const response = await axios.post(
                    `${api_endpoint}/clients/retrieveClient.php`,
                    {
                        client_id: id,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "application/json",
                        },
                    }
                );
                console.log(response);

                this.customerData = response.data.data;
                this.loading = false; // Turn off loading

                console.log(`Customer Data: `, response);
            } catch (error) {
                console.error("Error fetching customer data:", error);
                Swal.fire("Error", "Failed to load customer data.", "error");
                this.loading = false; // Turn off loading even on error
            }
        },
        openModal() {
            this.isOpen = true;
        },
        closeModal() {
            this.isOpen = false;
        },
    },
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