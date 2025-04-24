import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';

// Import your components for routes
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
import Model2 from './components/Dashboard/Model2.vue';
import Analytics from './components/Dashboard/Analytic.vue';
import AnalyticType from './components/Dashboard/AnalyticType.vue';
import DAX from './components/sections/DAX-page.vue';

// Import the store from store.js
import store from './store';

const routes = [
    // Public Routes
    { path: '/', name: 'Index' , component: Home,     meta: { showDax: false } },
    { path: '/login', name: 'Login', component: Login,     meta: { showDax: false } },
    { path: '/register', name: 'Register', component: Register,     meta: { showDax: false } },
    { path: '/otp-error', name: 'OTPError', component: OTPError,     meta: { showDax: false } },
    { path: '/otp-success', name: 'OTPSuccess', component: OTPSuccess,     meta: { showDax: false } },
    { path: '/otp', name: 'OTP', component: OTP,     meta: { showDax: false } },
    { path: '/forgot-password', name: 'Forgot', component: Forget,     meta: { showDax: false } },
    { path: '/reset-password', name: 'Reset', component: ResetPassword,     meta: { showDax: false } },
    
    // Protected Routes (Require Authentication)
    { path: '/profile', name: 'Profile', component: Profile, meta: { requiresAuth: true, requiresVerification: true, showDax: false } },
    { path: '/set-schedule', name: 'SetSchedule', component: SetSchedule, meta: { requiresAuth: true, requiresVerification: true, showDax: false } },
    { path: '/guild-profile', name: 'Guild', component: GuildProfile, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
    { path: '/notification', name: 'Notification', component: Notification, meta: { requiresAuth: true, requiresVerification: true, showDax: false } },
    { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
    { path: '/schedules', name: 'Schedules', component: Schedules, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
    { path: '/gig/:id', name: 'GigPage', component: GigIndex, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
    { path: '/gig-start', name: 'GigStart', component: GigStart, meta: { requiresAuth: true, requiresVerification: true, showDax: false } },
    { path: '/gig-end', name: 'GigEnd', component: GigEnd, meta: { requiresAuth: true, requiresVerification: true, showDax: false } },
    { path: '/gig-report/:id', name: 'GigReport', component: GigReport, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
    { path: '/gig/:gigId/repair/:repairId', name: 'Repair', component: GigRepair, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
    { path: '/model/:id/gig/:gigId?', name: 'Model', component: ModelPage, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
    { path: '/customer/:id/gig/:gigId', name: 'Customer', component: Customer, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
    { path: '/model2/:id/gig/:gigId?', name: 'Model2', component: Model2, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
    { path: '/analytics', name: 'Analytics', component: Analytics, meta: { requiresAuth: true, requiresVerification: true, showDax: false } },
    { path: '/analytics/type/:type?', name: 'AnalyticType', component: AnalyticType, meta: { requiresAuth: true, requiresVerification: true, showDax: false } },
    { path: '/dax', name: 'Dax', component: DAX, meta: { requiresAuth: true, requiresVerification: true, showDax: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation Guard to show/hide pre-loader and perform auth checks
router.beforeEach(async (to, from, next) => {
  store.dispatch('showLoader');
  const token = localStorage.getItem('token');
  const authPages = ['Login', 'Register', 'Home', 'OTP', 'Forget', 'ResetPassword'];
  console.log();
  // 1. If we have a token, block access to login/register/etc.
  if (token) {
    if ( authPages.includes(to.name)) {
      store.dispatch('hideLoader');
      return next('/dashboard');
    }

    // 2. If route is protected, verify the token (and user)
    if (to.meta.requiresAuth) {
      try {
        const { data } = await axios.get('/api/user', {
          headers: { Authorization: `Bearer ${token}` }
        });
        const isVerified = data.user.is_verified;

        store.commit('setUserID', data.user.id);


        if (to.meta.requiresVerification && !isVerified) {
          store.dispatch('hideLoader');
          return next('/otp');
        }

        store.dispatch('hideLoader');
        return next();
      } catch (err) {
        console.error('Auth check failed:', err);
        localStorage.removeItem('token');
        store.dispatch('hideLoader');
        return next('/login');
      }
    }

    // 3. Token exists but this is not a protected route → just proceed
    store.dispatch('hideLoader');
    return next();
  }

  // 4. No token: if trying to hit a protected route, send to login
  if (to.meta.requiresAuth) {
    store.dispatch('hideLoader');
    return next('/login');
  }

  // 5. No token + public route → proceed
  store.dispatch('hideLoader');
  next();
});

router.afterEach(() => {
    // Hide the pre-loader after route change
    store.dispatch('hideLoader');
});

export default router;
