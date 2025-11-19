// import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from '../router';

// Import sử dụng bootstrap trong toàn bộ dự án
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const app = createApp(App)
app.use(router)   // <-- phải đặt trước mount
app.mount('#app') // <-- mount sau khi đã dùng router
