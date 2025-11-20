
import {createRouter, createWebHistory } from 'vue-router';
import Login from '@/components/Auth/Login.vue';
import Register from '@/components/Auth/Register.vue';
import Home from '@/components/Client/Home.vue';

const routes = [
    {path: '/login', component: Login, name:'form-login'},
    {path: '/register', component: Register, name:'form-register'},
    {
        path: '/', 
        component: Home, 
        name:'app-home',
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Route guard - kiểm tra đăng nhập
router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('jwt_token');
    
    if (to.meta.requiresAuth && !isAuthenticated) {
        // Nếu trang cần đăng nhập nhưng chưa đăng nhập
        next('/login');
    } else if ((to.path === '/login' || to.path === '/register') && isAuthenticated) {
        // Nếu đã đăng nhập thì không cho vào trang login/register
        next('/');
    } else {
        next();
    }
});

export default router;