<template>
    <!-- Star Rating -->
    <div class="flex space-x-1 mt-3">
        <template v-for="(star, index) in 5" :key="index">
            <i v-if="index + 1 <= Math.floor(calculatedStars)" class="fas fa-star text-4xl text-[#232850FF]"></i>
            <i v-else-if="index < calculatedStars" class="fas fa-star-half-alt text-4xl text-[#232850FF]"></i>
            <i v-else class="far fa-star text-4xl text-gray-300"></i>
        </template>
    </div>
</template>

<script>

import axios from "axios"; // Ensure axios is imported


export default {
    name: "Ratings",
    data() {
        return {
            user_id: null,
            total_gig_price: 0.00,
            rating_rules: []
        };
    },
    computed: {
        calculatedStars() {
            if (!this.rating_rules.length) return 0;

            // Find the rule that matches the gig price
            const rule = this.rating_rules.find(rule =>
                this.total_gig_price >= rule.min_price && this.total_gig_price <= rule.max_price
            );

            return rule ? rule.stars : 1; // Default to 1 star if no rule matches
        }
    },
    created() {
        this.fetchUserData().then(() => {
            console.log("User ID after fetchUserData:", this.user_id);
            this.getRatingRules();
        });
    },
    methods: {
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
/* Add any additional styling here */
</style>
