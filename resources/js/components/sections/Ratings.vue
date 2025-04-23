<template>
    <div class="flex space-x-1 mt-3">
        <template v-for="index in 5" :key="index">
            <div class="relative inline-block cursor-pointer">
                <!-- Star Icon -->
                <i class="text-4xl" :class="[
                    index <= Math.floor(current_rating)
                        ? 'fas fa-star text-[#232850FF]'
                        : index - 0.5 === current_rating
                            ? 'fas fa-star-half-alt text-[#232850FF]'
                            : 'far fa-star text-gray-300'
                ]" @mouseenter="hoveredIndex = index" @mouseleave="hoveredIndex = null"
                    @click="activeTooltip = activeTooltip === index ? null : index">
                </i>

                <!-- Optional Tooltip -->
                <!-- <div v-if="hoveredIndex === index || activeTooltip === index"
                    class="absolute min-w-[100px] max-w-[180px] bottom-full mb-2 left-1/2 -translate-x-1/2 px-3 py-2 text-xs text-white bg-gray-900 rounded-lg shadow-md z-10 text-center">
                    Rated {{ index }} star{{ index > 1 ? 's' : '' }}
                </div> -->
            </div>
        </template>
    </div>
</template>



<script>

// [
//     { "id": 1, "min_price": 0, "max_price": 1499.99, "stars": 1 },
//     { "id": 2, "min_price": 1500, "max_price": 2999.99, "stars": 2 },
//     { "id": 3, "min_price": 3000, "max_price": 4499.99, "stars": 3 },
//     { "id": 4, "min_price": 4500, "max_price": 5999.99, "stars": 4 },
//     { "id": 5, "min_price": 6000, "max_price": 7499.99, "stars": 4.5 },
//     { "id": 6, "min_price": 7500, "max_price": 1000000, "stars": 5 }
// ]


import axios from "axios"; // Ensure axios is imported

export default {
    name: "Ratings",
    data() {
        return {
            user_id: null,
            total_gig_price: 0.00,
            rating_rules: [],
            hoveredIndex: null,
            activeTooltip: null,
            current_rating: 0
        };
    },
    mounted() {
        document.addEventListener("click", this.closeTooltipOnOutsideClick);
    },
    beforeUnmount() {
        document.removeEventListener("click", this.closeTooltipOnOutsideClick);
    },
    computed: {
        calculatedStars() {
            if (!this.rating_rules.length) return 0;

            // Find the rule that matches the gig price
            const rule = this.rating_rules.find(rule =>
                this.total_gig_price >= rule.min_price && this.total_gig_price <= rule.max_price
            );

            return rule ? rule.stars : 0; // Default to 1 star if no rule matches
        }
    },
    created() {
        this.fetchUserData().then(() => {
            this.getRatingRules();
        });
    },
    methods: {
        closeTooltipOnOutsideClick(event) {
            if (!event.target.closest(".inline-block")) {
                this.activeTooltip = null;
            }
        },
        async fetchUserData() {
            const token = localStorage.getItem('token');
            if (!token) {
                console.error("No token found in localStorage");
                return;
            }
            try {
                const response = await axios.get('/api/user', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                this.user_id = response.data.user.id;
                this.current_rating = response.data.user.user_rating;
                // Remove division by 100 if total_gig_price is already in dollars.
                this.total_gig_price = response.data.total_gig_price;
            } catch (error) {
                console.error("Error fetching user data:", error);
            }
        },
        async getRatingRules() {
            const token = localStorage.getItem('token');
            if (!token) {
                console.error("No token found in localStorage");
                return;
            }
            try {
                const response = await axios.get('/api/rating/rules', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                this.rating_rules = response.data;
            } catch (error) {
                console.error("Error fetching rating rules:", error);
            }
        }
    }
};
</script>
<style scoped>
.inline-block i {
    pointer-events: auto;
}
</style>