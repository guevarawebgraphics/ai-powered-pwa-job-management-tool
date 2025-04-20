<template>
    <!-- <div class="mt-20 mb-20 px-4 sm:px-6 lg:px-8" id="parentContainer"> -->
    <div class="mt-20 mb-20 pb-28 px-4 sm:px-6 lg:px-8" id="parentContainer">

        <NavBar />

        <!-- Guild Profile -->
        <div class="flex flex-col items-center p-6">
            <!-- Name & Title -->
            <div class="mt-3 text-center">
                <div class="relative inline-block">
                    <h2 class="text-xl text-[#171A1FFF]">Analytics</h2>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-6 border-gray-200">
            <div class="mb-6 flex space-x-2 justify-center overflow-x-auto whitespace-nowrap">
                <button v-for="(tab, index) in tabs" :key="index" @click="setActiveTab(tab.key)"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full cursor-pointer transition-colors duration-150 focus:outline-none"
                    :class="activeTab === tab.key
                        ? 'bg-gray-900 text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    {{ tab.label }}
                </button>
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

        <!-- All Charts View (Slider) -->
        <div v-else class="w-full max-w-md mx-auto">
            <swiper :slides-per-view="1" :space-between="16" :pagination="{ clickable: true }" :breakpoints="{
                640: { slidesPerView: 1 },
                768: { slidesPerView: 1.2 },
                1024: { slidesPerView: 1.5 }
            }">
                <swiper-slide v-for="(chart, index) in charts" :key="index">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="mb-4">
                            <h3 class="text-gray-500 text-sm font-medium mb-1">{{ chart.label }}</h3>
                            <div class="flex items-center space-x-2">
                                <h2 class="text-4xl font-bold text-gray-900 dark:text-white">65.5K</h2>
                            </div>
                            <p class="text-gray-500 text-sm mt-1">2.9K less than usual</p>
                        </div>
                        <div :id="chart.id" class="h-32"></div>
                    </div>
                </swiper-slide>
            </swiper>
        </div>


        <!-- Append here -->

        <!-- New Info Cards -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <!-- 1) Fastest Gig -->
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center space-x-2">
                    <!-- Font Awesome Icon -->
                    <i class="fas fa-clock text-lg text-gray-600 dark:text-gray-300"></i>
                    <span class="text-gray-800 dark:text-gray-100 text-sm">Fastest Gig</span>
                </div>
                <span class="text-gray-900 dark:text-white text-sm font-semibold">
                    12 Minutes
                </span>
            </div>
            <hr class="dark:border-gray-700" />

            <!-- 2) Most Valuable Gig -->
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-dollar-sign text-lg text-gray-600 dark:text-gray-300"></i>
                    <span class="text-gray-800 dark:text-gray-100 text-sm">Most Valuable Gig</span>
                </div>
                <span class="text-green-500 text-sm font-semibold">
                    $105
                </span>
            </div>
            <hr class="dark:border-gray-700" />

            <!-- 3) Most Gigs In A Day -->
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-fire text-lg text-gray-600 dark:text-gray-300"></i>
                    <span class="text-gray-800 dark:text-gray-100 text-sm">Most Gigs In A Day</span>
                </div>
                <span class="text-gray-900 dark:text-white text-sm font-semibold">
                    7
                </span>
            </div>
            <hr class="dark:border-gray-700" />

            <!-- 4) Average Daily Earn -->
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-chart-bar text-lg text-gray-600 dark:text-gray-300"></i>
                    <span class="text-gray-800 dark:text-gray-100 text-sm">Average Daily Earn</span>
                </div>
                <span class="text-green-500 text-sm font-semibold">
                    $220
                </span>
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
import { ref, onMounted, nextTick, watch } from "vue";
import NavBar from "../sections/Navbar.vue";
import BottomNav from "../sections/Bottombar.vue";
import ApexCharts from "apexcharts";

// Import Swiper components and styles
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

export default {
    name: "Analytic",
    components: { NavBar, BottomNav, Swiper, SwiperSlide },
    setup() {
        // Define our tabs (including an "all" option)
        const tabs = ref([
            { key: "all", label: "All" },
            { key: "tab1", label: "Revenue" },
            { key: "tab2", label: "Gigs" },
            { key: "tab3", label: "Job Time" },
        ]);

        // Data structure for the charts used in the slider (for the "All" tab)
        const charts = ref([
            { key: "tab1", label: "Revenue", id: "chart-tab1" },
            { key: "tab2", label: "Gigs", id: "chart-tab2" },
            { key: "tab3", label: "Job Time", id: "chart-tab3" },
        ]);

        // Shared ApexCharts configuration for all charts
        const chartOptions = {
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
                    "Mar 10", "Mar 11", "Mar 12", "Mar 13", "Mar 14", "Mar 15", "Mar 16",
                    "Mar 17", "Mar 18", "Mar 19", "Mar 20", "Mar 21", "Mar 22", "Apr 6",
                ],
                labels: {
                    show: true,
                    rotate: 0,
                    minHeight: 20,
                    style: {
                        fontSize: "10px",
                        colors: "#9ca3af",
                    },
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
                        return value === 0 ? "0" : value >= 1000 ? (value / 1000).toFixed(1) + "K" : value;
                    },
                    style: {
                        colors: "#9ca3af",
                        fontSize: "12px",
                    },
                },
            },
        };

        const activeTab = ref("tab1"); // default active tab

        // Helper: Return the chart container id for a tab key
        const getChartId = (tabKey) => {
            return `chart-${tabKey}`;
        };

        // Helper: Return the corresponding tab label as chart title
        const getChartTitle = (tabKey) => {
            const t = tabs.value.find((tab) => tab.key === tabKey);
            return t ? t.label : "";
        };

        // Function to initialize an ApexChart in the given container id
        const renderChart = (containerId) => {
            const container = document.getElementById(containerId);
            if (container) {
                new ApexCharts(container, chartOptions).render();
            }
        };

        const chartInstances = ref({});

        // Watch the active tab. When changed, wait until the DOM updates then initialize the chart(s).
        watch(activeTab, async (newVal, oldVal) => {
            if (oldVal && chartInstances.value[oldVal]) {
                chartInstances.value[oldVal].destroy();
                chartInstances.value[oldVal] = null;
            }

            await nextTick();

            if (newVal !== "all") {
                const container = document.getElementById(`chart-${newVal}`);
                if (container) {
                    chartInstances.value[newVal] = new ApexCharts(container, chartOptions);
                    chartInstances.value[newVal].render();
                }
            } else {
                for (const chartObj of charts.value) {
                    if (chartInstances.value[chartObj.key]) {
                        chartInstances.value[chartObj.key].destroy();
                        chartInstances.value[chartObj.key] = null;
                    }
                    const container = document.getElementById(chartObj.id);
                    if (!container) continue;
                    chartInstances.value[chartObj.key] = new ApexCharts(container, chartOptions);
                    chartInstances.value[chartObj.key].render();
                }
            }
        });

        // Initially render the chart of the default active tab
        onMounted(() => {
            if (activeTab.value !== "all") {
                renderChart(getChartId(activeTab.value));
            }
        });

        // Change the active tab
        const setActiveTab = (key) => {
            activeTab.value = key;
        };

        return {
            tabs,
            charts,
            activeTab,
            setActiveTab,
            getChartId,
            getChartTitle,
        };
    },
};
</script>

<style scoped>
/* Extra small devices (phones, less than 576px) */
@media (max-width: 575.98px) {
    /* Styles for phones */
    #parentContainer {
        max-width: 350px;
    }
}
</style>
