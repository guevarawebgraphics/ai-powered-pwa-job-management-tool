<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Repair Details Section -->
        <div class="max-w-lg mx-auto p-6">
            <h2 class="text-lg text-center text-[#232850]">
                Electric Dryer Tumbling , Not Heating
            </h2>
            <a href="#" class="block text-center text-blue-500 underline">WDW9500SS/AA-001</a>

            <div class="flex items-center mt-4">
                <i class="fas fa-wrench text-2xl text-gray-700"></i>
                <p class="text-sm text-red-600 ml-3">Most Common Repair</p>
                <p class="text-sm font-semibold text-red-600">
                    Replace the Heating Element
                </p>
            </div>

            <hr class="border-gray-300 my-4" />

            <!-- Gig Stats -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <i class="fas fa-headset text-2xl text-gray-700"></i>
                    <p class="text-sm font-medium mt-2">DAX</p>
                </div>
                <div class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <p class="text-lg font-bold text-gray-700">#1</p>
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

export default {
    components: { NavBar, BottomNav },
    name: "GigRepairPage",
    data() {
        return {};
    },
};
</script>
