import { createApp } from 'vue';
import axios from 'axios';
import ActiveOrders from './components/ActiveOrders.vue';

// Same-origin API. The demo Bearer token is injected by the Blade layout
// (see routes/web.php) — documented as an auth shortcut in the README.
const meta = (name) => document.querySelector(`meta[name="${name}"]`)?.content;

axios.defaults.baseURL = meta('api-base') ?? '/api';
const token = meta('api-token');
if (token) {
    axios.defaults.headers.common.Authorization = `Bearer ${token}`;
}
axios.defaults.headers.common.Accept = 'application/json';

createApp(ActiveOrders).mount('#app');
