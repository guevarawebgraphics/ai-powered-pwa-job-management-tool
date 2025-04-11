import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import store from './store';
import '/resources/css/styles.scss';
import axios from 'axios';
import './firebase';

// Set up Axios global defaults
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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
