<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Gig Report Section -->
        <div class="max-w-lg mx-auto px-4 py-6 space-y-6">
            <!-- Gig Header -->
            <div class="text-center">

                <h2 class="text-lg text-[#232850FF]">

                    Gig #{{ this.capitalizeWords(this.gigData.gig_cryptic) }}
                </h2>
                <div class="flex items-center space-x-3 mt-2">
                    <img :src="this.machineData.machine_photo" alt="Samsung Dryer" class="w-16 rounded-md" />
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ this.capitalizeWords(this.gigData.machine.machine_type) }} - {{
                            this.capitalizeWords(this.gigData.machine.brand_name) }}
                        </p>
                        <p class="text-xs text-gray-500">
                            **{{ this.gigData.initial_issue }}**
                        </p>
                        <p class="text-xs text-gray-500">
                            *{{ this.gigData.repair_notes }}*
                        </p>
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="flex justify-between items-center mt-5">
                <h3 class="text-lg text-[#232850FF]">{{ formattedCreatedAt }}</h3>
                <span class="text-lg text-[#232850FF]">{{ formattedCreatedTime }}</span>
            </div>

            <!-- Gig Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- <div
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start 
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <i class="fas fa-headset text-2xl text-gray-700"></i>
                    <p class="text-sm font-medium mt-2">DAX</p>
                </div> -->

                <!-- <DAX :page="'GigReport'" :user_id="techID" :vector_id="this.gigData.machine.vector_id" /> -->

                <button type="button" @click="openDax" v-if="page !== 'Model' && page !== 'GigReport'" class="bg-white min-h-[100px] rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px]
            border p-4 flex flex-col items-center justify-center text-center
             transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95
             focus:ring-2 focus:ring-gray-300">
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-headphones-simple text-lg text-[#171A1FFF]"></i>
                        <span class="text-xl font-medium text-[#666666FF]">DAX</span>
                    </div>
                </button>

                <div v-else-if="page === 'GigReport'" @click="openDax" class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px]
             border p-4 flex flex-col items-start cursor-pointer
             transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95
             focus:ring-2 focus:ring-gray-300">
                    <i class="fas fa-headset text-2xl text-gray-700"></i>
                    <p class="text-sm font-medium mt-2">DAX</p>
                </div>

                <div v-else @click="openDax"
                    class="bg-white shadow-md rounded-lg p-4 flex items-center justify-center mt-6 cursor-pointer">
                    <i class="fas fa-headset text-3xl text-gray-700"></i>
                    <p class="text-sm font-medium ml-2">DAX</p>
                </div>


                <div @click="openGigPotentialEarning()"
                    class="bg-white rounded-[12px] shadow-[rgba(100,100,111,0.2)_0px_7px_29px_0px] border p-4 flex flex-col items-start cursor-pointer
           transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300">
                    <p class="text-green-600 text-lg font-bold">${{ this.potentialGigPrice }}</p>
                    <p class="text-sm text-gray-500">Gig Potential</p>
                </div>
            </div>

            <!-- Post Gig Report -->
            <div class="text-center mt-6">
                <h3 class="text-lg">Post Gig Report</h3>
                <p class="text-sm text-gray-600">
                    **Complete Post Gig Report & Get Paid**
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">

                <!-- Diagnostic Button -->
                <button @click="selectDiagnostic" :class="[
                    'bg-white shadow-md rounded-lg p-4 flex flex-col items-center transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300 relative overflow-hidden',
                    selectedOptionType === 'diagnostic' ? 'bg-gray-200 ring-2 ring-gray-400' : ''
                ]">
                    <i class="fas fa-tools text-2xl"
                        :class="selectedOptionType === 'diagnostic' ? 'text-black' : 'text-[#666666FF]'"></i>
                    <p class="text-sm font-medium"
                        :class="selectedOptionType === 'diagnostic' ? 'text-black' : 'text-[#666666FF]'">
                        Diagnostic
                    </p>

                    <!-- Animated Check Mark -->
                    <transition name="fade">
                        <div v-if="selectedOptionType === 'diagnostic'" class="absolute top-2 right-2">
                            <i class="fas fa-check-circle text-green-500 text-lg animate-ping"></i>
                            <i class="fas fa-check-circle text-green-500 text-lg absolute top-0 left-0"></i>
                        </div>
                    </transition>
                </button>

                <!-- Full Repair Button -->
                <button @click="selectFullRepair" type="button" :class="[
                    'bg-white shadow-md rounded-lg p-4 flex flex-col items-center transition-all duration-200 ease-in-out transform hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-300',
                    selectedOptionType === 'full-repair' ? 'bg-gray-200 ring-2 ring-gray-400' : ''
                ]">
                    <i class="fas fa-wrench text-2xl"
                        :class="selectedOptionType === 'full-repair' ? 'text-black' : 'text-[#666666FF]'"></i>
                    <p class="text-sm font-medium"
                        :class="selectedOptionType === 'full-repair' ? 'text-black' : 'text-[#666666FF]'">
                        Full Repair
                    </p>
                </button>

            </div>

            <p v-if="this.gigData.gig_complete != 3" class="text-sm text-center text-[#666666FF] mt-5">
                Select Diagnostic or Full Repair
            </p>

            <div class="text-center mt-4">
                <p v-if="this.gigData.gig_complete != 3" class="text-green-600 text-sm font-semibold">
                    Paid by Check? Submit Photo of Check Now
                </p>
                <div v-if="this.gigData.gig_complete != 3" class="flex justify-center space-x-6 mt-2">
                    <input type="file" ref="fileInput" multiple accept="image/*" class="hidden"
                        @change="handleFileUpload" />

                    <button @click="openCamera()" class="flex flex-col items-center text-blue-500">
                        <i class="fas fa-camera text-2xl"></i>
                        <p class="text-xs">Take Photo</p>
                    </button>

                    <button @click="triggerFileInput" class="flex flex-col items-center text-blue-500">
                        <i class="fas fa-image text-2xl"></i>
                        <p class="text-xs">Select Image</p>
                    </button>
                </div>

                <!-- Image Previews (Click to View) -->
                <div v-if="previewImages.length" class="mt-5 flex justify-center">
                    <div class="flex flex-wrap gap-2 justify-center">
                        <div v-for="(image, index) in previewImages" :key="index" class="relative">
                            <img :src="image" class="w-16 h-16 object-cover rounded cursor-pointer"
                                @click="openImageViewer(image)" />
                            <button @click="removeImage(index)"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full text-xs p-1">✕</button>
                        </div>
                    </div>
                </div>

            </div>



            <!-- Repair Selection -->
            <div class="text-center mt-6">
                <h3 v-if="this.gigData.gig_complete != 3" class="text-lg">Select All That Apply Below</h3>
                <h3 v-else class="text-lg">Selected all applicable options below</h3>
                <p class="text-sm text-gray-500">
                    Top 5 Most Common Repairs & Diagnostics
                </p>
            </div>


            <div class="space-y-4 mt-4">
                <div v-if="numberedRepairs.length > 0" v-for="repair in numberedRepairs" :key="repair.repairName">
                    <label
                        class="relative bg-white rounded-lg shadow-md border p-4 cursor-pointer flex items-start space-x-4">
                        <!-- Checkbox positioned in the top-right corner -->
                        <input type="checkbox" name="repair_solution[]"
                            class="absolute top-4 right-4 w-5 h-5 border-gray-400 rounded-md" :value="repair.id"
                            v-model="selectedRepairs" />

                        <div style="margin-left: unset;">
                            <p class="text-lg font-bold text-repairName-700">
                                #{{ repair.number }} {{ repair.repairName }}
                            </p>
                            <p class="text-gray-700 text-sm"><strong>Symptoms:</strong> {{ repair.symptoms }}</p>
                            <p class="text-gray-500 text-xs mt-1"><strong>Solution:&nbsp;</strong>
                                <br>
                                <span v-html="repair.solution.replace(/\n/g, '<br/>')"></span>
                            </p>
                            <p class="text-gray-500 text-xs"><strong>Parts Needed:</strong> {{
                                repair.partsNeeded.join(", ")
                                }}</p>
                        </div>
                    </label>
                </div>
                <!-- Show this message when no repairs are found -->
                <p v-else class="text-gray-600 text-center mt-4">No Common Repairs and Diagnostics Found</p>
            </div>

            <div class="space-y-4 mt-4">


                <!-- <div v-if="textarea_content_array.length > 0" v-for="(repair, index) in textarea_content_array"
                    :key="repair.content">
                    <label
                        class="relative bg-white rounded-lg shadow-md border p-4 cursor-pointer flex items-start space-x-4">
                        <i class="fas fa-trash text-red-600 text-lg font-semibold absolute top-4 right-4 w-5 h-5"
                            @click="removeTextareaContent(index)"></i>

                        <div style="margin-left: unset;">
                            <p class="text-gray-700 text-sm"><strong>Content:</strong> {{ repair.content }}</p>

                            <div class="flex flex-wrap gap-2 mt-3">
                                <div v-for="(image, index) in repair.images" :key="index" class="relative">
                                    <img :src="image.url" :alt="image.filename"
                                        class="w-20 h-20 object-cover rounded-md" @click="openImageViewer(image.url)" />
                                </div>
                            </div>

                        </div>
                    </label>
                </div> -->

                <div v-if="textarea_content_array.length > 0" v-for="(repair, index) in textarea_content_array"
                    :key="repair.content">
                    <label
                        class="relative bg-white rounded-lg shadow-md border p-4 cursor-pointer flex items-start space-x-4">
                        <!-- Checkbox in the top-right corner -->
                        <input type="checkbox" v-model="repair.selected"
                            class="absolute top-4 right-4 w-5 h-5 border-gray-400 rounded-md" />
                        <div style="margin-left: unset;">
                            <p class="text-gray-700 text-sm"><strong>Content:</strong> {{ repair.content }}</p>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <div v-for="(image, idx) in repair.images" :key="idx" class="relative">
                                    <img :src="image.url" :alt="image.filename"
                                        class="w-20 h-20 object-cover rounded-md" @click="openImageViewer(image.url)" />
                                </div>
                            </div>
                        </div>
                    </label>
                </div>



            </div>


            <div class="space-y-4 mt-4 ">
                <div class="relative bg-white rounded-lg shadow-md border p-4 cursor-pointer flex flex-col space-y-4">
                    <div style="margin-left: unset;">
                        <p class="text-lg font-bold text-gray-700"><i class="fa-solid fa-question"></i> Other</p>
                    </div>
                    <div class="flex flex-wrap gap-2" v-if="this.textarea_images.length>0">
                        <div v-for="(image, index) in textarea_images" :key="index" class="relative">
                            <img :src="image.url" :alt="image.filename" class="w-20 h-20 object-cover rounded-md"
                                @click="openImageViewer(image.url)" />
                            <button type="button"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full text-xs p-1"
                                @click="removeOpenTextImage(index)">
                                ✕
                            </button>
                        </div>
                    </div>


                    <div class="flex flex-wrap items-center relative space-x-2">
                        <button class="p-2 text-[#262025FF]" @click="openCameraV2()">
                            <i class="fas fa-camera text-2xl"></i>
                        </button>
                        <button type="button" class="p-2 text-[#262025FF]" @click="$refs.multipleFileInput.click()">
                            <i class="fas fa-image text-2xl"></i>
                            <input type="file" multiple ref="multipleFileInput" class="hidden"
                                @change="openTextUploadImage" />
                        </button>

                        <!-- Input field with emoji -->
                        <div class="relative flex-1 min-w-0">
                            <input type="text" placeholder="Aa"
                                class="mx-3 bg-gray-100 outline-none text-gray-700 placeholder-[#BCC1CA] w-full p-2 rounded-full pr-10"
                                v-model="textarea_content">
                            <button @click="showEmojiPicker = !showEmojiPicker"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                😊
                            </button>
                        </div>

                        <!-- Emoji Picker (positioned absolutely as before) -->
                        <emoji-picker v-if="showEmojiPicker" @select="addEmoji" class="absolute bottom-12 right-0" />

                        <button class="p-2 text-[#262025FF]" @click="addOtherRepair()">
                            <i class="fa-regular fa-paper-plane text-2xl"></i>
                        </button>
                    </div>


                </div>
            </div>


            <!-- Submit Button -->
            <button class="w-full bg-red-600 text-white py-3 rounded-lg mt-6 text-lg font-semibold"
                @click="submitGigReport" v-if="this.gigData.gig_complete != 3">
                Submit Report
            </button>
            <button class="w-full bg-green-600 text-white py-3 rounded-lg mt-6 text-lg font-semibold" disabled v-else>
                Gig Completed & Report Submitted
            </button>
        </div>

        <BottomNav />
    </div>


    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-80">
            <h2 class="text-lg font-bold mb-4">Select Part Installed</h2>

            <div class="space-y-3">
                <label v-for="(part, index) in parts" :key="index" class="flex items-center justify-between">
                    <span class="text-gray-700">{{ part.name }}</span>
                    <input type="checkbox" v-model="part.selected" class="w-5 h-5 rounded border-gray-300">
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <button @click="closeModal" class="text-gray-500 text-sm font-medium">Cancel</button>


                <button @click="confirmSelection" class="bg-[#232850FF] text-white px-4 py-2 rounded-lg text-sm">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div v-if="cameraOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-4 rounded-lg shadow-lg text-center">
            <video ref="video" autoplay class="w-64 h-48 border rounded"></video>
            <canvas ref="canvas" class="hidden"></canvas>

            <div class="mt-4 space-x-2">
                <button @click="capturePhoto" class="bg-green-500 text-white px-4 py-2 rounded">Capture</button>
                <button @click="closeCamera" class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>


    <!-- Camera Modal -->
    <div v-if="cameraOpenV2" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-4 rounded-lg shadow-lg text-center">
            <video ref="videoV2" autoplay class="w-64 h-48 border rounded"></video>
            <canvas ref="canvasV2" class="hidden"></canvas>

            <div class="mt-4 space-x-2">
                <button @click="capturePhotoV2" class="bg-green-500 text-white px-4 py-2 rounded">Capture</button>
                <button @click="closeCameraV2" class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
            </div>
        </div>
    </div>


    <!-- Full-Screen Image Viewer -->
    <div v-if="viewingImage" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center">
        <div class="relative">
            <img :src="viewingImage" class="max-w-full max-h-screen rounded-lg shadow-lg" />
            <button @click="closeImageViewer"
                class="absolute top-4 right-4 bg-white text-black rounded-full text-xl px-3 py-1 font-bold">✕</button>
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
import EmojiPicker from "vue3-emoji-picker";
import "vue3-emoji-picker/css";
import { bus } from '../sections/DAX.vue'; 

export default {
    // components: { NavBar, BottomNav, EmojiPicker },
    components: { NavBar, BottomNav, EmojiPicker },
    name: "GigReportPage",
    data() {
        return {
            parts: [
                { name: "Heating Element", selected: false },
                { name: "PCB Board", selected: true }, // Default checked
                { name: "Terminal block", selected: false },
                { name: "Motor", selected: false },
                { name: "Fuse", selected: false },
            ],
            selectedParts: [],
            repairHelp: [],
            count: 1,
            gigData: [],
            machineData: [],
            gigID: null,
            modelNumber: null,
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
            isOpen: false,
            images: [],
            previewImages: [],
            cameraOpen: false,
            cameraOpenV2: false,
            viewingImage: null, // Stores the image to be viewed
            selectedOptionType: null, // Track which button is selected
            selectedRepairs: [],
            gig_report_images: [],
            gig_resolution: [],
            textarea_content: "",
            textarea_content_array: [],
            textarea_images: [],
            showEmojiPicker: false,
            gigPotentialEarnings: [],
            potentialGigPrice: 0.00,
            isGigPotentialOpen: false,
            techID: null

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
                return "View Post Gig Report";
            }
        },
        buttonClass() {
            if (!this.gigData.time_started) {
                return "bg-green-600 hover:bg-green-700"; // Green for Start
            } else {
                return "bg-red-600 hover:bg-red-700"; // Red for End and Submit Report
            }
        },
        page() {
            return this.$route.name;
        }
    },
    methods: {
        capitalizeWords(str) {
            if (!str) return ''; // Return an empty string if str is undefined/null
            return str.replace(/\b\w/g, char => char.toUpperCase());
        },
        goToModel(modelID) {
            this.$router.push(`/model/${modelID}`);
        },
        goToRepair(repairId, gigId) {
            this.$router.push(`/gig/${gigId}/repair/${repairId}`);
        },
        goToCustomer(id) {
            this.$router.push(`/customer/${id}`);
        },
        async gigDetail(gigID) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const api_endpoint_main = import.meta.env.VITE_API_ENDPOINT_MAIN;
                const token = import.meta.env.VITE_API_KEY;

                const response = await axios.get(`${api_endpoint}/gigs/retrieveGigByGigID.php?gig_id=${gigID}`, {
                    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                });

                this.gigData = response.data.data[0];
                this.techID = this.gigData.assigned_tech_id;
                this.modelNumber = this.gigData.model_number;
                this.machineDetail(this.modelNumber);


                if (this.gigData.top_recommended_repairs) {
                    try {
                        const parsedData = JSON.parse(this.gigData.top_recommended_repairs);

                        this.repairHelp = parsedData;
                    } catch (error) {
                        console.error("Error parsing common_repairs JSON:", error);
                        this.repairHelp = [];
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

                // if (this.machineData.common_repairs) {
                //     try {
                //         const parsedData = JSON.parse(this.machineData.common_repairs);

                //         this.repairHelp = parsedData;
                //     } catch (error) {
                //         console.error("Error parsing common_repairs JSON:", error);
                //         this.repairHelp = [];
                //     }
                // }
            } catch (error) {
                console.error("Error fetching repair history data:", error);
            }
        },
        openModal() {
            this.isOpen = true;
        },
        closeModal() {
            this.isOpen = false;
        },
        confirmSelection() {
            const selectedParts = this.parts.filter(part => part.selected).map(part => part.name);
            console.log("Selected Parts:", selectedParts);
            this.selectedParts = selectedParts;
            this.closeModal();
        },
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        handleFileUpload(event) {
            const files = event.target.files;
            if (files.length) {
                this.images = [...files];

                // Preview images
                this.previewImages = [];
                Array.from(files).forEach((file) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previewImages.push(e.target.result);
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Reset file input to allow re-adding the same image
            this.$refs.fileInput.value = "";
        },
        removeImage(index) {
            this.previewImages.splice(index, 1);
            this.images.splice(index, 1);
        },
        async openCamera() {
            this.cameraOpen = true;
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                this.$refs.video.srcObject = stream;
            } catch (error) {
                console.error("Error accessing camera:", error);
                this.cameraOpen = false;
            }
        },
        capturePhoto() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            const context = canvas.getContext("2d");

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert to data URL and save to previewImages
            const imageData = canvas.toDataURL("image/png");
            this.previewImages.push(imageData);

            // Close camera after capture
            this.closeCamera();
        },
        closeCamera() {
            this.cameraOpen = false;
            const video = this.$refs.video;
            const stream = video.srcObject;
            if (stream) {
                const tracks = stream.getTracks();
                tracks.forEach((track) => track.stop());
            }
            video.srcObject = null;
        },
        async openCameraV2() {
            this.cameraOpenV2 = true;
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                this.$refs.videoV2.srcObject = stream;
            } catch (error) {
                console.error("Error accessing camera:", error);
                this.cameraOpenV2 = false;
            }
        },
        // capturePhotoV2() {
        //     const video = this.$refs.videoV2;
        //     const canvas = this.$refs.canvasV2;
        //     const context = canvas.getContext("2d");

        //     canvas.width = video.videoWidth;
        //     canvas.height = video.videoHeight;
        //     context.drawImage(video, 0, 0, canvas.width, canvas.height);

        //     // Convert to data URL and save to previewImages
        //     const imageData = canvas.toDataURL("image/png");
        //     this.textarea_images.push(imageData);

        //     // Close camera after capture
        //     this.closeCameraV2();
        // },

        async capturePhotoV2() {
            const video = this.$refs.videoV2;
            const canvas = this.$refs.canvasV2;
            const context = canvas.getContext("2d");

            // Set canvas dimensions
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert canvas to Blob (image file)
            canvas.toBlob(async (blob) => {
                if (!blob) return;

                const file = new File([blob], `captured_${Date.now()}.png`, { type: "image/png" });

                // Call API to upload image
                const formData = new FormData();
                formData.append('image', file);

                const token = localStorage.getItem("token");

                try {
                    const response = await axios.post('/api/temporary/image/upload', formData, {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            'Content-Type': 'multipart/form-data'
                        },
                    });

                    if (response.data.url) {
                        this.textarea_images.push({
                            url: response.data.url,
                            filename: response.data.filename // Store filename for deletion
                        });

                        console.log(`textarea_images: `, this.textarea_images);
                    }
                } catch (error) {
                    console.error('Upload failed:', error);
                }
            }, "image/png");

            // Close camera after capture
            this.closeCameraV2();
        },

        closeCameraV2() {
            this.cameraOpenV2 = false;
            const video = this.$refs.videoV2;
            const stream = video.srcObject;
            if (stream) {
                const tracks = stream.getTracks();
                tracks.forEach((track) => track.stop());
            }
            video.srcObject = null;
        },

        openImageViewer(image) {
            this.viewingImage = image;
        },

        closeImageViewer() {
            this.viewingImage = null;
        },

        selectFullRepair() {
            this.selectedOptionType = "full-repair";
            this.isOpen = true; // Open modal
        },

        selectDiagnostic() {
            this.selectedOptionType = "diagnostic";
        },

        async submitGigReport() {
            try {
                // ✅ Show a confirmation prompt before submission
                const result = await Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to submit the report as finalized?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, submit it!",
                    cancelButtonText: "No, cancel",
                });

                // If the user cancels, stop execution
                if (!result.isConfirmed) {
                    return;
                }

                const api_endpoint = import.meta.env.VITE_API_ENDPOINT_MAIN;
                const token = localStorage.getItem("token");

                const formData = new FormData();
                formData.append("type", this.selectedOptionType);
                formData.append("selectedRepairs", JSON.stringify(this.selectedRepairs));
                formData.append("selectedParts", JSON.stringify(this.selectedParts));
                formData.append("gig_id", this.gigID);

                // Convert textarea_content_array to JSON and append
                // formData.append("addtl_recommended_repairs", JSON.stringify(this.textarea_content_array));

                // Filter additional repairs to include only the ones that are checked
                const filteredAdditionalRepairs = this.textarea_content_array.filter(item => item.selected);
                formData.append("addtl_recommended_repairs", JSON.stringify(filteredAdditionalRepairs));

                // Append general images separately
                this.images.forEach((image, index) => {
                    console.log(`wow2: `, image);
                    formData.append(`images[${index}]`, image);
                });

                console.log("FormData entries:");
                for (const pair of formData.entries()) {
                    console.log(pair[0], pair[1]);
                }

                const response = await axios.post(`${api_endpoint}/api/gig/report`, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "multipart/form-data",
                    },
                });

                console.log("Response:", response);

                // ✅ Show success message if API call succeeds
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "Gig report submitted successfully.",
                });

                this.$router.push(`/gig/${this.gigID}`);

                return;

            } catch (error) {
                console.error("Error:", error);

                // ❌ Handle validation errors (422 Unprocessable Entity)
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;

                    // Convert errors into an HTML unordered list
                    let errorList = "<ul style='text-align: center;'>";
                    Object.values(errors).forEach(errorArray => {
                        errorArray.forEach(errorMessage => {
                            errorList += `<li>❌${errorMessage}</li>`;
                        });
                    });
                    errorList += "</ul>";

                    // Show error messages in SweetAlert
                    Swal.fire({
                        icon: "error",
                        title: "Validation Error!",
                        html: errorList, // Use "html" instead of "text" for formatting
                    });
                } else {
                    // ❌ Handle other API errors
                    Swal.fire({
                        icon: "error",
                        title: "Oops!",
                        text: "Something went wrong. Please try again later.",
                    });
                }
            }
        },

        async openTextUploadImage(event) {
            const files = event.target.files;
            if (!files.length) return; // Exit if no file is selected

            const token = localStorage.getItem("token");

            for (let file of files) {
                const formData = new FormData();
                formData.append('image', file);

                try {
                    const response = await axios.post('/api/temporary/image/upload', formData, {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            'Content-Type': 'multipart/form-data'
                        },
                    });

                    if (response.data.url) {
                        this.textarea_images.push({
                            url: response.data.url,
                            filename: response.data.filename // Store filename for deletion
                        });

                        console.log(`textarea_images: `, this.textarea_images);
                    }
                } catch (error) {
                    console.error('Upload failed:', error);
                }
            }

            // Reset file input to allow re-uploading the same file
            event.target.value = "";
        },

        async removeOpenTextImage(index) {
            const image = this.textarea_images[index];

            const token = localStorage.getItem("token");

            if (image.filename) { // Ensure we have a filename to delete
                try {

                    const response = await axios.post('/api/temporary/image/delete-image', {
                        filename: image.filename
                    },{
                        headers: {
                            Authorization: `Bearer ${token}`,
                            'Content-Type': 'multipart/form-data'
                        },
                    });

                    // Remove from Vue.js state after successful backend deletion
                    this.textarea_images.splice(index, 1);

                    console.log(`textarea_images: `, this.textarea_images);
                } catch (error) {
                    console.error('Failed to delete image:', error);
                }
            }
        },

        addEmoji(emoji) {
            console.log(`emoji: `, emoji.i);
            this.textarea_content += emoji.i;
            // this.textarea_content += emoji;
        },

        // addOtherRepair() {
        //     const payload = {
        //         content: this.textarea_content || '', // Ensure content is at least an empty string
        //         images: Array.isArray(this.textarea_images) ? [...this.textarea_images] : [] // Ensure it's always an array
        //     };

        //     this.textarea_content_array.push(payload);

        //     this.textarea_content = '';
        //     this.textarea_images = [];

        //     console.log(payload);
        // },

        addOtherRepair() {
            const payload = {
                content: this.textarea_content || '',
                images: Array.isArray(this.textarea_images) ? [...this.textarea_images] : [],
                selected: true // Automatically checked by default
            };

            this.textarea_content_array.push(payload);

            this.textarea_content = '';
            this.textarea_images = [];

            console.log(payload);
        },
        async removeTextareaContent(index) {
            console.log("this.textarea_content_array:", this.textarea_content_array);
            console.log("Removing index:", index);

            if (!this.textarea_content_array[index]) {
                console.error(`Error: Index ${index} is out of bounds`);
                return;
            }

            const images = this.textarea_content_array[index].images;
            const token = localStorage.getItem("token");

            for (const image of images) {
                try {
                    await axios.post('/api/temporary/image/delete-image', {
                        filename: image.filename
                    }, {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            'Content-Type': 'multipart/form-data'
                        },
                    });
                    console.log(`Deleted: ${image.filename}`);
                } catch (error) {
                    console.error(`Failed to delete: ${image.filename}`, error);
                }
            }

            // Ensure the index is still valid before removing
            if (this.textarea_content_array[index]) {
                this.textarea_content_array.splice(index, 1);
            }
        },
        openGigPotentialEarning() {
            this.isGigPotentialOpen = true;
        },
        openDax() {
            bus.emit('open-dax')
        }




    }
};
</script>
