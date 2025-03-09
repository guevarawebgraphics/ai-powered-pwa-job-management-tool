<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Model Information -->
        <div class="max-w-lg mx-auto p-6">
            <h2 class="text-lg font-medium text-center text-[#232850FF]">
                {{ this.machineData.model_number }}
            </h2>

            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-washer text-2xl text-gray-700"></i>
                    <div>
                        <p class="text-sm text-gray-800 font-medium text-[#222222FF]">{{ this.machineData.brand_name }}</p>
                        <p class="text-xs text-[#666666FF]">{{ this.machineData.machine_type }}</p>
                    </div>
                </div>
                <img src="../../../../public/images/dryer.png" alt="Dryer" class="w-24 rounded-md" />
            </div>

            <!-- DAX Section -->
            <div class="bg-white shadow-md rounded-lg p-4 flex items-center justify-center mt-6">
                <i class="fas fa-headset text-2xl text-gray-700"></i>
                <p class="text-sm font-medium ml-2">DAX</p>
            </div>

            <!-- Useful Links -->
            <div class="space-y-4 mt-6">
                <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
                    <i class="fas fa-book text-xl text-gray-700"></i>
                    <p class="ml-3 text-sm font-medium text-[#232850FF]">Service Manual</p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
                    <i class="fas fa-diagram-project text-xl text-gray-700"></i>
                    <p class="ml-3 text-sm font-medium text-[#232850FF]">Parts Diagram</p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
                    <i class="fas fa-hand-point-up text-xl text-gray-700"></i>
                    <p class="ml-3 text-sm font-medium text-[#232850FF]">Service Pointers</p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
                    <i class="fas fa-file-alt text-xl text-gray-700"></i>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-[#232850FF]">Internal Notes</p>
                        <p class="text-xs text-gray-600">
                            Difficult UI removal on this model
                        </p>
                        <p class="text-xs text-gray-600">
                            Most common repair is Heating Element
                        </p>
                    </div>
                </div>
            </div>

            <!-- Common Repair Videos -->
            <h3 class="text-lg font-medium text-center mt-6 text-[#232850FF]">
                Common Repair Videos
            </h3>
            <div class="space-y-4 mt-3">
                <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                </div>
                <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                </div>
                <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
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
    name: "ModelPage",
    data() {
        return {
            modelID: null,
            machineData: []
        };
    },
    created() {
        this.modelID = this.$route.params.id;

        if (this.modelID) {
            this.modelDetail(this.modelID);
        }
    },
    watch: {
        // Watch for changes in route (if navigating to another customer)
        '$route.params.id'(modelID) {
            modelDetail(modelID);
        }
    },
    methods: {
        async modelDetail(modelID) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const token = import.meta.env.VITE_API_KEY;

                const response = await axios.get(`${api_endpoint}/machines/retrieveMachineByID.php`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

                this.machineData = response.data.data;

                console.log(this.machineData);

            } catch (error) {
                console.error("Error fetching gig history data:", error);
            }
        }
    }
};
</script>
