import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios'; // Import Axios for API calls
import Home from './components/Home.vue'; 
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import OTP from './components/OTP.vue';
import OTPError from './components/OTPError.vue';
import OTPSuccess from './components/OTPSuccess.vue';

// Dashboard (Protected Routes)
import Profile from './components/Dashboard/Profile.vue';
import SetSchedule from './components/Dashboard/SetSchedule.vue';
import GuildProfile from './components/Dashboard/GuildProfile.vue';
import Notification from './components/Dashboard/Notification.vue';
import Dashboard from './components/Dashboard/Index.vue';
import Schedules from './components/Dashboard/Schedule.vue';
import GigStart from './components/Dashboard/GigStart.vue';
import GigEnd from './components/Dashboard/GigEnd.vue';
import GigReport from './components/Dashboard/GigReport.vue';
import GigRepair from './components/Dashboard/Repair.vue';
import ModelPage from './components/Dashboard/Model.vue';
import Customer from './components/Dashboard/CustomerUI.vue';
import GigIndex from './components/Dashboard/GigIndex.vue';
import Forget from './components/Forget.vue';
import ResetPassword from './components/ResetPassword.vue';

const routes = [
    // Public Routes
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    { path: '/otp-error', component: OTPError },
    { path: '/otp-success', component: OTPSuccess },
    { path: '/otp', component: OTP },
    { path: '/forgot-password', component: Forget },
    { path: '/reset-password', component: ResetPassword },

    // Protected Routes (Require Authentication)
    { path: '/profile', component: Profile, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/set-schedule', component: SetSchedule, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/guild-profile', component: GuildProfile, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/notification', component: Notification, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/schedules', component: Schedules, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/gig/:id', component: GigIndex, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/gig-start', component: GigStart, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/gig-end', component: GigEnd, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/gig-report/:id', component: GigReport, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/gig/:gigId/repair/:repairId', component: GigRepair, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/model/:id/gig/:gigId?', component: ModelPage, meta: { requiresAuth: true, requiresVerification: true } },
    { path: '/customer/:id/gig/:gigId', component: Customer, meta: { requiresAuth: true, requiresVerification: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// 🔐 Navigation Guard to Protect Routes
router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem('token');

    if (to.meta.requiresAuth) {
        if (!token) {
            return next('/login'); // ❌ Not authenticated → Redirect to Login
        }

        try {
            // ✅ Check user verification status from API
            const response = await axios.get('/api/user', {
                headers: { Authorization: `Bearer ${token}` }
            });

            const user = response.data.user;
            const isVerified = user.is_verified; // Assuming backend returns this

            if (to.meta.requiresVerification && !isVerified) {
                return next('/otp'); // ❌ Not verified → Redirect to OTP
            }

            next(); // ✅ Authenticated & verified → Allow access
        } catch (error) {
            console.error('Auth check failed:', error);
            localStorage.removeItem('token'); // Remove invalid token
            return next('/login'); // ❌ Redirect to Login on error
        }
    } else {
        next(); // ✅ Public routes
    }
});


export default router;
