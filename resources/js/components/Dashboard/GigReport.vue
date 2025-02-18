<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Gig Report Section -->
        <div class="max-w-lg mx-auto p-6">
            <!-- Gig Title -->
            <h2 class="text-xl text-center text-[#232850]">
                Gig #R-LDNH307
            </h2>
            <div class="flex items-center mt-2">
                <img src="../../../../public/images/washing-machine.png" alt="Samsung Dryer"
                    class="w-16 h-16 rounded-md" />
                <div class="ml-3">
                    <p class="text-sm font-semibold text-gray-800">
                        Samsung Dryer No Heat
                    </p>
                    <p class="text-xs text-gray-500">
                        **Check Power at Terminal Block for 240V**
                    </p>
                    <p class="text-xs text-gray-500">
                        *Verify Symptoms, Diagnose Entire Heating Circuit*
                    </p>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="flex justify-between items-center mt-4">
                <h3 class="text-lg text-gray-700">June, 12</h3>
                <h3 class="text-lg text-gray-700">11:00 AM</h3>
            </div>

            <!-- Gig Stats -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <i class="fas fa-headset text-2xl text-gray-700"></i>
                    <p class="text-sm font-medium mt-2">DAX</p>
                </div>
                <div class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <p class="text-green-600 text-lg font-bold">$125</p>
                    <p class="text-sm text-gray-500">Gig Potential</p>
                </div>
            </div>

            <!-- Post Gig Report -->
            <div class="text-center mt-6">
                <h3 class="text-lg">Post Gig Report</h3>
                <p class="text-sm text-gray-600">
                    **Complete Post gig report And get Paid**
                </p>
            </div>

            <!-- Diagnostic / Full Repair Selection -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <button class="bg-white shadow-md rounded-lg p-4 flex flex-col items-center">
                    <i class="fas fa-tools text-2xl text-[#666666FF]"></i>
                    <p class="text-sm font-medium text-[#666666FF]">Diagnostic</p>
                </button>
                <button class="bg-white shadow-md rounded-lg p-4 flex flex-col items-center">
                    <i class="fas fa-wrench text-2xl text-[#666666FF]"></i>
                    <p class="text-sm font-medium text-[#666666FF]">Full Repair</p>
                </button>
            </div>
            <p class="text-sm text-center text-[#666666FF] mt-2">
                Select Diagnostic or Full Repair
            </p>

            <!-- Submit Check Image -->
            <div class="text-center mt-4">
                <p class="text-green-600 text-sm font-semibold">
                    Paid by Check? Submit Photo of Check Now
                </p>
                <div class="flex justify-center space-x-6 mt-2">
                    <button class="flex flex-col items-center text-blue-500">
                        <i class="fas fa-camera text-2xl"></i>
                        <p class="text-xs">Take Photo</p>
                    </button>
                    <button class="flex flex-col items-center text-blue-500">
                        <i class="fas fa-image text-2xl"></i>
                        <p class="text-xs">Select Image</p>
                    </button>
                </div>
            </div>

            <!-- Repair Selection -->
            <div class="text-center mt-6">
                <h3 class="text-lg">Select All That Apply Below</h3>
                <p class="text-sm text-gray-500">
                    Top 5 Most Common Repairs & Diagnostics
                </p>
            </div>

            <!-- Repair Checklist -->
            <div class="mt-4 space-y-4">
                <div v-for="(repair, index) in repairs" :key="index"
                    class="bg-white shadow-md rounded-lg p-4 flex justify-between items-center">
                    <div>
                        <p class="text-lg font-bold text-gray-700">
                            {{ index + 1 }}
                        </p>
                        <p class="text-sm text-gray-600">{{ repair.title }}</p>
                    </div>
                    <input type="checkbox" class="w-5 h-5 border-gray-400 rounded-md" />
                </div>
            </div>

            <!-- Submit Button -->
            <button class="w-full bg-red-600 text-white py-3 rounded-lg mt-6 text-lg font-semibold">
                Submit Report
            </button>
        </div>

        <BottomNav />
    </div>
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";

export default {
    components: { NavBar, BottomNav },
    name: "GigReportPage",
    data() {
        return {
            repairs: [
                {
                    title: "35 Minutes from Your Current Location - Contact Andrea NOW",
                },
                {
                    title: "35 Minutes from Your Current Location - 2519 SE Ocean Dr, Jensen Beach, FL 34957",
                },
                {
                    title: "Samsung Dryer - Model #: WD9500SS/AA-001, Serial: SN1600005756923",
                },
                {
                    title: "Samsung Dryer - Model #: WD9500SS/AA-001, Serial: SN1600005756923",
                },
                {
                    title: "Samsung Dryer - Model #: WD9500SS/AA-001, Serial: SN1600005756923",
                },
                { title: "Other" },
            ],
        };
    },
};
</script>
