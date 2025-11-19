
import {createRouter, createWebHistory } from 'vue-router';
import Login from '@/components/Auth/Login.vue';
import Register from '@/components/Auth/Register.vue';

import Home from '@/components/Client/Home.vue';


// import App from '@/App.vue'

const routes = [
    {path: '/login', component: Login, name:'form-login'},
    {path: '/register', component: Register, name:'form-register'},
    {path: '/', component: Home, name:'app-home'}
];

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router;