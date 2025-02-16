<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <div class="max-w-lg mx-auto px-4 py-6 space-y-6">
            <!-- Gig Header -->
            <div class="text-center">
                <h2 class="text-lg text-[#232850FF]">
                    Gig #R-LDNH307
                </h2>
                <div class="flex items-center justify-center space-x-3 mt-2">
                    <img src="../../../../public/images/washing-machine.png" alt="Samsung Dryer"
                        class="w-12 h-12 rounded-md" />
                    <div class="text-sm text-gray-600 text-left">
                        <p class="font-semibold">Samsung Dryer No Heat</p>
                        <p class="text-gray-500">
                            **Check Power at Terminal Block for 240V**
                        </p>
                        <p class="text-gray-500">
                            *Verify Symptoms, Diagnose Entire Heating Circuit*
                        </p>
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="flex justify-between items-center">
                <h3 class="text-lg text-[#232850FF]">June, 12</h3>
                <span class="text-lg text-[#232850FF]">11:00 AM</span>
            </div>

            <!-- Earnings & Gig Potential -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-lg shadow-md border p-4 flex flex-col items-center">
                    <i class="fas fa-headset text-lg text-gray-700"></i>
                    <span class="text-md mt-1">DAX</span>
                </div>
                <div class="bg-white rounded-lg shadow-md border p-4 flex flex-col items-center">
                    <span class="text-xl font-bold text-green-500">$125</span>
                    <p class="text-sm text-gray-500">Gig Potential</p>
                </div>
            </div>

            <!-- Start Button -->
            <button class="w-full bg-red-600 text-white py-2 rounded-lg font-semibold hover:bg-red-700">
                END
            </button>

            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-md border p-4 space-y-3">
                <!-- Top Section -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <!-- Profile Icon -->
                        <i class="fas fa-id-card text-gray-500 text-3xl"></i>
                        <div>
                            <p class="font-bold text-lg text-gray-900">Andrea Ortiz</p>
                            <p class="text-sm text-gray-500">**Return Customer**</p>
                            <p class="text-xs text-gray-500">*Dog’s Name: Biscuit*</p>
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
                    <p class="text-gray-600 text-md">Contact Andrea NOW</p>
                    <p class="text-gray-500">772-232-9596</p>
                </div>
            </div>

            <!-- Customer Address -->
            <div class="bg-white rounded-lg shadow-md border p-4 flex items-start space-x-3">
                <!-- Location Icon -->
                <i class="fas fa-map-marker-alt text-gray-500 text-3xl"></i>
                <div>
                    <p class="text-gray-700 text-sm font-semibold">35 Minutes from Your Current Location</p>
                    <p class="text-gray-600">
                        2519 SE Ocean Dr. <br />
                        Jensen Beach, FL 34957
                    </p>
                </div>
            </div>

            <!-- Samsung Dryer Details -->
            <div class="bg-white rounded-lg shadow-md border p-4 flex items-start space-x-3">
                <!-- Appliance Icon -->
                <i class="fas fa-tshirt text-gray-500 text-3xl"></i>
                <div>
                    <p class="text-gray-700 text-sm font-semibold">Samsung Dryer</p>
                    <p class="text-gray-600">Model #: WDW9500SS/AA-001</p>
                    <p class="text-gray-500">Serial: SN1600005756923</p>
                </div>
            </div>


            <!-- Customer Input -->
            <div class="text-center mt-6 border-b pb-5">
                <h3 class="text-lg text-[#232850FF] underline">
                    Customer Input
                </h3>
                <p class="text-sm text-gray-600 mt-2">
                    The dryer is turning on but not drying the clothes. It
                    Tumbles but it does not seem to get hot.
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
                    <div v-for="(repair, index) in repairHelp" :key="index"
                        class="bg-white rounded-lg shadow-md border p-4">
                        <p class="text-lg font-bold text-gray-700">
                            {{ index + 1 }}
                        </p>
                        <p class="text-gray-700 text-sm">{{ repair.title }}</p>
                        <p class="text-gray-500 text-xs">
                            {{ repair.details }}
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

export default {
    components: { NavBar, BottomNav },
    name: "GigEndPage",
    data() {
        return {
            repairHelp: [
                {
                    title: "Contact Andrea NOW",
                    details: "772-232-9596",
                },
                {
                    title: "Jensen Beach Location",
                    details: "2519 SE Ocean Dr, Jensen Beach, FL 34957",
                },
                {
                    title: "Samsung Dryer",
                    details:
                        "Model #: WDW9500SS/AA-001, Serial: SN1600005756923",
                },
                {
                    title: "Samsung Dryer",
                    details:
                        "Model #: WDW9500SS/AA-001, Serial: SN1600005756923",
                },
                {
                    title: "Samsung Dryer",
                    details:
                        "Model #: WDW9500SS/AA-001, Serial: SN1600005756923",
                },
            ],
        };
    },
};
</script>
