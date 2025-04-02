<template>
    <div class="mt-20 mb-20">
        <NavBar />

        <!-- Model Information -->
        <div class="w-[350px] mx-auto p-6">
            <h2 class="text-lg font-medium text-center text-[#232850FF]">
                {{ this.machineData.model_number }}
            </h2>

            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-washer text-2xl text-gray-700"></i>
                    <div>
                        <p class="text-sm text-gray-800 font-medium text-[#222222FF]">
                            {{ this.capitalizeWords(this.machineData.brand_name) }}
                        </p>
                        <p class="text-xs text-[#666666FF]">
                            {{ this.capitalizeWords(this.machineData.machine_type) }}
                        </p>
                    </div>
                </div>
                <img :src="machineData.machine_photo" :alt="this.machineData.machine_type" class="w-24 rounded-md" />
            </div>

            <!-- DAX Section -->
            <div class="bg-white shadow-md rounded-lg p-4 flex items-center justify-center mt-6">
                <i class="fas fa-headset text-3xl text-gray-700"></i>
                <p class="text-sm font-medium ml-2">DAX</p>
            </div>

            <!-- Useful Links (Accordion) -->
            <div class="space-y-4 mt-6">

                <div class="bg-white shadow-md rounded-lg p-4 cursor-pointer">
                    <div class="flex items-center justify-between"
                        @click="isOpen.serviceManual = !isOpen.serviceManual">
                        <div class="flex items-center">
                            <i class="fas fa-book text-3xl text-gray-700"></i>
                            <p class="ml-3 text-sm font-medium text-[#232850FF]">Service Manual</p>
                        </div>
                        <i :class="['fas', isOpen.serviceManual ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                    </div>
                    <div v-if="isOpen.serviceManual" class="mt-5 text-sm text-gray-700">
                        <ul v-if="serviceManual.length">
                            <li v-for="file in serviceManual" :key="file.file_name">
                                <div class="flex items-center bg-white shadow-md rounded-lg p-4 mb-4">
                                    <i class="fas fa-clipboard-list text-2xl text-gray-500"></i>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-800">{{ file.file_name }}</p>
                                        <a :href="file.url" target="_blank"
                                            class="text-blue-600 hover:underline font-medium">
                                            View Here
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <p v-else class="text-gray-500 italic text-center">No files found for Service Manual.</p>
                    </div>
                </div>

                <div class="bg-white shadow-md rounded-lg p-4 cursor-pointer">
                    <div class="flex items-center justify-between" @click="isOpen.partsDiagram = !isOpen.partsDiagram">
                        <div class="flex items-center">
                            <i class="fas fa-diagram-project text-3xl text-gray-700"></i>
                            <p class="ml-3 text-sm font-medium text-[#232850FF]">Parts Diagram</p>
                        </div>
                        <i :class="['fas', isOpen.partsDiagram ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                    </div>
                    <div v-if="isOpen.partsDiagram" class="mt-5 text-sm text-gray-700">
                        <ul v-if="partsDiagram.length">
                            <li v-for="file in partsDiagram" :key="file.file_name">
                                <div class="flex items-center bg-white shadow-md rounded-lg p-4 mb-4">
                                    <i class="fas fa-clipboard-list text-2xl text-gray-500"></i>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-800">{{ file.file_name }}</p>
                                        <a :href="file.url" target="_blank"
                                            class="text-blue-600 hover:underline font-medium">
                                            View Here
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <p v-else class="text-gray-500 italic text-center">No files found for Parts Diagram.</p>
                    </div>
                </div>

                <div class="bg-white shadow-md rounded-lg p-4 cursor-pointer">
                    <div class="flex items-center justify-between"
                        @click="isOpen.servicePointers = !isOpen.servicePointers">
                        <div class="flex items-center">
                            <i class="fas fa-hand-point-up text-3xl text-gray-700"></i>
                            <p class="ml-3 text-sm font-medium text-[#232850FF]">Service Pointers</p>
                        </div>
                        <i :class="['fas', isOpen.servicePointers ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                    </div>
                    <div v-if="isOpen.servicePointers" class="mt-5 text-sm text-gray-700">
                        <ul v-if="servicePointers.length">
                            <li v-for="file in servicePointers" :key="file.file_name">
                                <div class="flex items-center bg-white shadow-md rounded-lg p-4 mb-4">
                                    <i class="fas fa-clipboard-list text-2xl text-gray-500"></i>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-800">{{ file.file_name }}</p>
                                        <a :href="file.url" target="_blank"
                                            class="text-blue-600 hover:underline font-medium">
                                            View Here
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <p v-else class="text-gray-500 italic text-center">No files found for Service Pointers.</p>
                    </div>
                </div>





                <!-- <div class="bg-white shadow-md rounded-lg p-4 flex items-center">
                    <i class="fas fa-file-alt text-xl text-gray-700"></i>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-[#232850FF]">Internal Notes</p>
                        <p class="text-xs text-gray-600">Difficult UI removal on this model</p>
                        <p class="text-xs text-gray-600">Most common repair is Heating Element</p>
                    </div>
                </div>

 -->


                <div class="bg-white shadow-md rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-file-alt text-3xl text-gray-700"></i>
                        <p class="ml-3 text-sm font-medium text-[#232850FF]">Internal Notes</p>
                    </div>

                    <div v-if="machineData.machine_notes && machineData.machine_notes.records.length" class="mt-5">
                        <div v-for="(note, index) in machineData.machine_notes.records" :key="index"
                            class="mt-2 p-2 border-l-4 border-blue-500 bg-gray-100 rounded">
                            <p class="text-xs text-gray-800"><strong>Note:</strong> {{ note.notes }}</p>
                            <p class="text-xs text-gray-600"><strong>Technician:</strong> {{ note.technician }}</p>
                            <p class="text-xs text-gray-600"><strong>Date:</strong> {{ formatDate(note.date) }}</p>
                        </div>
                    </div>

                    <p v-else class="text-xs text-gray-500 mt-2">No internal notes available.</p>
                </div>


            </div>


            <!-- Common Repair Videos -->
            <h3 class="text-lg font-medium text-center mt-6 text-[#232850FF]">
                Common Repair Videos
            </h3>
            <div class="space-y-4 mt-3">
                <!-- <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                </div>
                <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                </div>
                <div class="bg-gray-200 w-full h-40 flex items-center justify-center rounded-md">
                    <i class="fas fa-play-circle text-4xl text-gray-600"></i>
                </div> -->

                <!-- <div class="bg-gray-200 w-full h-32 flex items-center justify-center rounded-lg">
                        <i class="fas fa-play-circle text-4xl text-gray-500"></i>
                    </div> -->
                <!-- Loop through each video URL -->
                <div v-for="(videoLink, index) in repairVideo" :key="index"
                    class="bg-gray-200 w-full h-32 flex items-center justify-center rounded-lg">
                    <iframe :src="transformToEmbedUrl(videoLink)" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen class="w-full h-32 rounded-lg"></iframe>
                </div>
            </div>

        </div>

        <BottomNav />
    </div>
</template>

<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import axios from "axios";

export default {
    components: { NavBar, BottomNav },
    name: "ModelPage",
    data() {
        return {
            repairHelp: [],
            repairVideo: [],
            modelID: null,
            machineData: [],
            isOpen: {
                serviceManual: false,
                partsDiagram: false,
                servicePointers: false
            },
            serviceManual: [],
            partsDiagram: [],
            servicePointers: [],
            noServiceManualMessage: null,
            noPartsDiagramMessage: null,
            noServicePointersMessage: null
        };
    },
    created() {
        this.modelID = this.$route.params.id;

        if (this.modelID) {
            this.modelDetail(this.modelID);
        }
    },
    watch: {
        '$route.params.id'(modelID) {
            this.modelDetail(modelID);
        }
    },
    methods: {
        capitalizeWords(str) {
            if (!str) return ''; // Return an empty string if str is undefined/null
            return str.replace(/\b\w/g, char => char.toUpperCase());
        },
        async modelDetail(modelID) {
            try {
                const api_endpoint = import.meta.env.VITE_API_ENDPOINT;
                const main_api_endpoint = import.meta.env.VITE_API_ENDPOINT_MAIN;
                const token = import.meta.env.VITE_API_KEY;

                const response = await axios.get(
                    `${api_endpoint}/machines/retrieveMachineByID.php?modelNumber=${modelID}`,
                    {
                        headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" }
                    }
                );

                // Store machine data
                this.machineData = response.data.data;

                if (this.machineData.common_repairs) {
                    try {
                        const parsedData = JSON.parse(this.machineData.common_repairs);
                        this.repairHelp = parsedData;
                        this.repairVideo = []; // Initialize the array

                        // Loop through each repair object
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

                // Parse machine_notes if it's a string
                if (typeof this.machineData.machine_notes === "string") {
                    try {
                        this.machineData.machine_notes = JSON.parse(this.machineData.machine_notes);
                    } catch (error) {
                        console.error("Error parsing machine_notes JSON:", error);
                        this.machineData.machine_notes = { records: [] }; // Fallback to empty array
                    }
                }

                try {
                    const service_manual = await axios.get(
                        `${main_api_endpoint}/api/machine-files/${this.machineData.machine_type}/${this.machineData.brand_name}/${this.machineData.model_number}/PartsList`,
                        { headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" } }
                    );

                    if (service_manual.data.error || service_manual.data.files.length === 0) {
                        this.serviceManual = [];
                        this.noServiceManualMessage = "No files found for Service Manual.";
                    } else {
                        this.serviceManual = service_manual.data.files;
                        this.noServiceManualMessage = ""; // Clear message if files exist
                    }
                } catch (error) {
                    console.error("Error fetching Service Manual:", error);
                    this.serviceManual = [];
                    this.noServiceManualMessage = "No files found for Service Manual.";
                }

                try {
                    const parts_diagram = await axios.get(
                        `${main_api_endpoint}/api/machine-files/${this.machineData.machine_type}/${this.machineData.brand_name}/${this.machineData.model_number}/TechSheet`,
                        { headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" } }
                    );

                    if (parts_diagram.data.error || parts_diagram.data.files.length === 0) {
                        this.partsDiagram = [];
                        this.noPartsDiagramMessage = "No files found for Parts Diagram.";
                    } else {
                        this.partsDiagram = parts_diagram.data.files;
                        this.noPartsDiagramMessage = "";
                    }
                } catch (error) {
                    console.error("Error fetching Parts Diagram:", error);
                    this.partsDiagram = [];
                    this.noPartsDiagramMessage = "No files found for Parts Diagram.";
                }

                try {
                    const service_pointers = await axios.get(
                        `${main_api_endpoint}/api/machine-files/${this.machineData.machine_type}/${this.machineData.brand_name}/${this.machineData.model_number}/ServicePointers`,
                        { headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" } }
                    );

                    if (service_pointers.data.error || service_pointers.data.files.length === 0) {
                        this.servicePointers = [];
                        this.noServicePointersMessage = "No files found for Service Pointers.";
                    } else {
                        this.servicePointers = service_pointers.data.files;
                        this.noServicePointersMessage = "";
                    }
                } catch (error) {
                    console.error("Error fetching Service Pointers:", error);
                    this.servicePointers = [];
                    this.noServicePointersMessage = "No files found for Service Pointers.";
                }



            } catch (error) {
                console.error("Error fetching machine data:", error);
            }
        },
        formatDate(dateString) {
            if (!dateString) return "Unknown date";
            const options = { year: "numeric", month: "long", day: "numeric" };
            return new Date(dateString).toLocaleDateString("en-US", options);
        },
        toggleSection(section) {
            this.isOpen[section] = !this.isOpen[section];
        },
        transformToEmbedUrl(youtubeUrl) {
            // If the URL already uses the embed format, return it directly.
            if (youtubeUrl.includes("embed")) {
                return youtubeUrl;
            }
            let videoId = "";
            // Check if the URL contains "watch?v="
            if (youtubeUrl.includes("watch?v=")) {
                const parts = youtubeUrl.split("watch?v=");
                videoId = parts[1].split("&")[0]; // Remove any extra query parameters
            } else if (youtubeUrl.includes("youtu.be/")) {
                const parts = youtubeUrl.split("youtu.be/");
                videoId = parts[1].split("?")[0];
            }
            return videoId ? `https://www.youtube.com/embed/${videoId}` : youtubeUrl;
        }
    }
};
</script>
