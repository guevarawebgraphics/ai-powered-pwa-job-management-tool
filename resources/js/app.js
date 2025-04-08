import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import '/resources/css/styles.scss';
import axios from 'axios'; // Import Axios
import './firebase';
import { createStore } from 'vuex';

// Set up Axios global defaults
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Register Axios interceptor globally
// axios.interceptors.response.use(
//     response => response,
//     error => {
//         if (error.response && error.response.status === 401) {
//             // Clear the token from localStorage and redirect to login.
//             localStorage.removeItem('token');
//             window.location.href = '/login';
//         }
//         return Promise.reject(error);
//     }
// );

// Create the Vuex store
const store = createStore({
    state: {
        notificationData: [],
        chatMessages: [],
        unseenNotification: 0
    },
    mutations: {
        setNotificationData(state, payload) {
            state.notificationData = payload;
        },
        setChatMessages(state, payload) {
            state.chatMessages = payload;
        },
        setUnseenNotification(state, payload) {
            state.unseenNotification = payload;
        },
        addChatMessage(state, message) {
            state.chatMessages.push(message);
        },
    },
    actions: {
        updateNotificationData({ commit }, payload) {
            commit('setNotificationData', payload);
        },
        updateChatMessages({ commit }, payload) {
            commit('setChatMessages', payload);
        },
        updateUnseenNotification({ commit }, payload) {
            commit('setUnseenNotification', payload);
        },
        addChatMessage({ commit }, message) {
            commit('addChatMessage', message);
        },
    },
});

const app = createApp(App);
app.use(router);
app.use(store);
app.mount('#app');

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/serviceworker.js')
            .then((reg) => console.log('Service Worker registered!', reg))
            .catch((err) => console.log('Service Worker registration failed!', err));
    });
}
