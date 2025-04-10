<template>
    <div class="mt-20 mb-20 px-4 sm:px-6 lg:px-8">
        <NavBar />

        <div class="flex flex-col items-center p-6">
            <div class="mt-3 text-center">
                <div class="relative inline-block">
                    <!-- Here, just access pageName directly (no 'this.') -->
                    <h2 v-if="pageName === 'gigs'" class="text-xl text-[#171A1FFF]">Gigs</h2>
                    <h2 v-else-if="pageName === 'job-time'" class="text-xl text-[#171A1FFF]">Job Time</h2>
                    <h2 v-else-if="pageName === 'revenue'" class="text-xl text-[#171A1FFF]">Revenue</h2>
                </div>
            </div>
        </div>

        <!-- Chart Content -->
        <div v-if="activeTab !== 'all'" class="w-full max-w-md mx-auto">
            <!-- Single Chart View -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <!-- Chart Header -->
                <div class="mb-4">
                    <h3 class="text-gray-500 text-sm font-medium mb-1">
                        {{ getChartTitle(activeTab) }}
                    </h3>
                    <div class="flex items-center space-x-2">
                        <h2 class="text-4xl font-bold text-gray-900 dark:text-white">65.5K</h2>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">2.9K less than usual</p>
                </div>
                <!-- Chart Container -->
                <div :id="getChartId(activeTab)" class="h-32"></div>
            </div>
        </div>


        <div class="max-w-lg mx-auto mt-10 mb-5">
            <div class="flex justify-between items-center mb-4">
                <h2 v-if="pageName === 'gigs'" class="text-xl text-[#171A1FFF]">Gigs Earnings </h2>
                <h2 v-else-if="pageName === 'job-time'" class="text-xl text-[#171A1FFF]">Revenue</h2>
                <h2 v-else-if="pageName === 'revenue'" class="text-xl text-[#171A1FFF]">Job Time</h2>

                <!-- Date Picker -->
                <div class="relative">
                    <select v-model="selectedDate"
                        class="border border-gray-600 text-gray-700 text-sm px-3 py-1.5 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="0" selected>Last 24 Hours</option>
                        <option value="1">Last 5 Days</option>
                        <option value="2">Last 7 Days</option>
                        <option value="3">Last 30 Days</option>
                        <option value="4">Last 180 Days</option>
                        <option value="5">Last 365 Days</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="max-w-lg mx-auto mt-5">
            <div class="space-y-4 mt-3">

                <div v-for="(update, index) in machine_type" :key="index" @click="toggleExpand(index)"
                    class="bg-white rounded-[12px] shadow-md shadow-[#171a1f17] drop-shadow-sm border p-4 flex flex-col space-y-2 relative">
                    <!-- ^^^ relative so the arrow can be placed absolutely inside this container ^^^ -->

                    <div class="flex items-start space-x-3">
                        <!-- Image/Icon -->
                        <img v-if="update.image" :src="update.image" class="w-12 h-12 rounded-md" />

                        <!-- Content -->
                        <div class="flex-1">
                            <p class="text-normal font-medium text-[#222222FF]">
                                {{ update.label }}
                                <span v-if="pageName === 'job-time'">Time</span>
                                <span v-else>Revenue</span>
                            </p>
                            <p class="text-normal font-medium text-[#222222FF]">
                                <span class="text-green-500 font-bold text-sm flex items-center">
                                    $0.00
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Toggle Arrow: now absolutely positioned in the top-right corner of the container -->
                    <i class="fas fa-chevron-down text-xl text-gray-500 cursor-pointer transition-transform duration-300 
           absolute top-4 right-4" :class="{ 'rotate-180': expandedIndex === index }"></i>

                    <!-- Expanded Content -->
                    <div v-if="expandedIndex === index" class="mt-2 p-2 rounded-md">
                        <p class="text-sm text-gray-700">
                            This is additional information about "{{ update.label }}".
                            You can add more details here.
                        </p>
                    </div>
                </div>

            </div>
        </div>


        <!-- AFTER your existing charts or info cards -->
        <!-- (e.g. right above <BottomNav />) -->

        <div class="mt-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Level Up</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Watch Videos to Level Up your Skills
            </p>

            <!-- Video List Container -->
            <div class="mt-4 space-y-3">
                <!-- 1) LG Washer -->
                <div class="flex items-center space-x-3 p-3 rounded-lg bg-white dark:bg-gray-800 shadow">
                    <img src="../../../../public/images/selection-0.png" alt="LG Washer"
                        class="w-14 h-14 object-cover rounded-md" />
                    <span class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">
                        LG Washer
                    </span>
                </div>

                <!-- 2) LG Washer Not Spinning -->
                <div class="flex items-center space-x-3 p-3 rounded-lg bg-white dark:bg-gray-800 shadow">
                    <img src="../../../../public/images/selection-1.png" alt="LG Washer Not Spinning"
                        class="w-14 h-14 object-cover rounded-md" />
                    <span class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">
                        LG Washer Not Spinning
                    </span>
                </div>

                <!-- 3) Washing Machine Off -->
                <div class="flex items-center space-x-3 p-3 rounded-lg bg-white dark:bg-gray-800 shadow">
                    <img src="../../../../public/images/selection-2.png" alt="Washing Machine Off"
                        class="w-14 h-14 object-cover rounded-md" />
                    <span class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">
                        Washing Machine Off
                    </span>
                </div>

                <!-- 4) LG Washer Troubleshooting -->
                <div class="flex items-center space-x-3 p-3 rounded-lg bg-white dark:bg-gray-800 shadow">
                    <img src="../../../../public/images/selection-3.png" alt="LG Washer Troubleshooting"
                        class="w-14 h-14 object-cover rounded-md" />
                    <span class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">
                        LG Washer Troubleshooting
                    </span>
                </div>

                <!-- 5) Fix Dishwasher not Draining -->
                <div class="flex items-center space-x-3 p-3 rounded-lg bg-white dark:bg-gray-800 shadow">
                    <img src="../../../../public/images/selection-4.png" alt="Fix Dishwasher not Draining"
                        class="w-14 h-14 object-cover rounded-md" />
                    <span class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">
                        Fix Dishwasher not Draining
                    </span>
                </div>
            </div>
        </div>



        <BottomNav />
    </div>
</template>
<script>
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import ApexCharts from "apexcharts";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

export default {
    name: "GigPage",
    components: {
        NavBar,
        BottomNav,
        Swiper,
        SwiperSlide
    },

    // ------------------------------
    // 1) Data
    // ------------------------------
    data() {
        return {

            expandedIndex: null,

            machine_type: [
                { key: "dishwashers", label: "Diswasher", image: "/images/machine/dishwashers.png" },
                { key: "dryers", label: "Dryer", image: "/images/machine/dryers.png" },
                { key: "microwaves", label: "Microwave", image: "/images/machine/microwaves.png" },
                { key: "refrigerators", label: "Refrigerator", image: "/images/machine/refrigerators.png" },
                { key: "stoves", label: "Stove", image: "/images/machine/stoves.png" },
                { key: "washers", label: "Washer", image: "/images/machine/washers.png" },
            ],
            // Route-based
            pageName: null,

            // Tabs
            tabs: [
                { key: "all", label: "All" },
                { key: "revenue", label: "Revenue" },
                { key: "gigs", label: "Gigs" },
                { key: "job-time", label: "Job Time" },
            ],
            activeTab: null,

            // Chart config
            chartOptions: {
                chart: {
                    type: "line",
                    height: 160,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: "Inter, sans-serif",
                },
                series: [
                    {
                        name: "Gigs",
                        data: [2300, 2100, 3300, 2400, 2200, 2700, 2100, 2600, 2800, 2500, 2700, 2100, 2200, 3300],
                    },
                ],
                stroke: {
                    curve: "smooth",
                    width: 3,
                    colors: ["#6366f1"],
                },
                tooltip: {
                    enabled: true,
                    theme: "light",
                    x: { show: true },
                },
                grid: {
                    show: true,
                    strokeDashArray: 4,
                    borderColor: "#e5e7eb",
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } },
                    padding: { bottom: 10, top: 0, left: 0, right: 0 },
                },
                xaxis: {
                    categories: [
                        "Mar 10", "Mar 11", "Mar 12", "Mar 13", "Mar 14", "Mar 15",
                        "Mar 16", "Mar 17", "Mar 18", "Mar 19", "Mar 20", "Mar 21",
                        "Mar 22", "Apr 6"
                    ],
                    labels: {
                        show: true,
                        rotate: 0,
                        minHeight: 20,
                        style: { fontSize: "10px", colors: "#9ca3af" },
                        formatter: function (value, _, index) {
                            if (index === 0 || index === 13) {
                                return value;
                            }
                            return " ";
                        },
                    },
                    axisTicks: { show: false },
                    axisBorder: { show: false },
                },
                yaxis: {
                    show: true,
                    tickAmount: 3,
                    min: 0,
                    max: 3300,
                    labels: {
                        formatter: (value) => {
                            return value === 0 ? "0" : value >= 1000
                                ? (value / 1000).toFixed(1) + "K"
                                : value;
                        },
                        style: {
                            colors: "#9ca3af",
                            fontSize: "12px",
                        },
                    },
                },
            },

            // For storing chart instances if needed
            chartInstances: {},

            // If you have multiple chart containers:
            charts: [
                { key: "revenue", label: "Revenue", id: "revenue" },
                { key: "gigs", label: "Gigs", id: "gigs" },
                { key: "job-time", label: "Job Time", id: "job-time" },
            ],
        };
    },

    // ------------------------------
    // 2) Computed
    // ------------------------------
    computed: {
        // Example of a computed property that returns chart title 
        // based on your active tab
        currentTabTitle() {
            const t = this.tabs.find((tab) => tab.key === this.activeTab);
            return t ? t.label : "";
        },
    },

    // ------------------------------
    // 3) Watch
    // ------------------------------
    watch: {
        // Whenever the route changes, update your pageName & activeTab
        '$route.params.type'(newVal) {
            this.pageName = newVal;
            this.activeTab = newVal;
            this.setActiveTab(newVal);
        },

        // React to changes in activeTab - e.g., destroy old chart & render new one
        activeTab: async function (newVal, oldVal) {
            // Destroy old chart if it exists
            if (oldVal && this.chartInstances[oldVal]) {
                this.chartInstances[oldVal].destroy();
                this.chartInstances[oldVal] = null;
            }

            // Wait for DOM updates
            await this.$nextTick();

            // Render new chart(s)
            if (newVal !== "all") {
                const container = document.getElementById(`chart-${newVal}`);
                if (container) {
                    this.chartInstances[newVal] = new ApexCharts(container, this.chartOptions);
                    this.chartInstances[newVal].render();
                }
            } else {
                // Render all charts
                for (const chartObj of this.charts) {
                    if (this.chartInstances[chartObj.key]) {
                        this.chartInstances[chartObj.key].destroy();
                        this.chartInstances[chartObj.key] = null;
                    }
                    const container = document.getElementById(chartObj.id);
                    if (!container) continue;
                    this.chartInstances[chartObj.key] = new ApexCharts(container, this.chartOptions);
                    this.chartInstances[chartObj.key].render();
                }
            }
        },
    },

    // ------------------------------
    // 4) Lifecycle Hooks
    // ------------------------------
    created() {
        // Initialize pageName (from route param)
        this.pageName = this.$route.params.type;
        this.activeTab = this.pageName; // sets the default selected tab
        this.setActiveTab(this.activeTab);
    },

    mounted() {
        // If there's a default tab that isn’t "all", render its chart
        if (this.activeTab !== "all") {
            this.renderChart(this.getChartId(this.activeTab));
        }

        this.setActiveTab(this.activeTab);
    },

    // ------------------------------
    // 5) Methods
    // ------------------------------
    methods: {
        setActiveTab(tabKey) {
            this.activeTab = tabKey;
        },

        getChartId(tabKey) {
            return `chart-${tabKey}`;
        },

        getChartTitle(tabKey) {
            const t = this.tabs.find((tab) => tab.key === tabKey);
            return t ? t.label : "";
        },

        renderChart(containerId) {
            const container = document.getElementById(containerId);
            if (container) {
                new ApexCharts(container, this.chartOptions).render();
            }
        },
        toggleExpand(index) {
            this.expandedIndex = this.expandedIndex === index ? null : index;
        },
    },
};
</script>

<style scoped>
/* Additional custom styling can go here */
</style>

<style scoped>
/* Additional custom styling can go here */
</style>
