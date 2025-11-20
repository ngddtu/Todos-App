<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '@/api';

const router = useRouter();
const route = useRoute();
const currentUser = ref(null);
const isAuthenticated = computed(() => !!localStorage.getItem('jwt_token'));

// Load user info từ localStorage hoặc API
const loadUser = async () => {
    const userStr = localStorage.getItem('user');
    if (userStr) {
        currentUser.value = JSON.parse(userStr);
    } else if (isAuthenticated.value) {
        // Nếu có token nhưng chưa có user info, gọi API
        try {
            const res = await api.get('/api/me');
            if (res.data.user) {
                currentUser.value = res.data.user;
                localStorage.setItem('user', JSON.stringify(res.data.user));
            }
        } catch (error) {
            // Token có thể đã hết hạn
            logout();
        }
    }
};

const logout = async () => {
    try {
        await api.post('/api/logout');
    } catch (error) {
        // Ignore error nếu token đã hết hạn
    } finally {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('user');
        currentUser.value = null;
        router.push('/login');
    }
};

onMounted(() => {
    loadUser();
});

// Watch route changes để load user khi navigate
router.afterEach(() => {
    loadUser();
});
</script>

<template>
    <nav class="navbar navbar-dark bg-dark border-bottom border-secondary">
        <div class="container-fluid">
            <router-link to="/" class="navbar-brand">
                <i class="bi bi-list-check text-primary me-2"></i>
                Todo App
            </router-link>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Khi đã đăng nhập -->
                <template v-if="isAuthenticated && currentUser">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle text-light fs-4"></i>
                        <span class="text-light">{{ currentUser.name }}</span>
                    </div>
                    <button @click="logout" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Đăng xuất
                    </button>
                </template>
                
                <!-- Khi chưa đăng nhập -->
                <template v-else>
                    <router-link :to="{name:'form-login'}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Đăng nhập
                    </router-link>
                    <router-link :to="{name:'form-register'}" class="btn btn-primary btn-sm">
                        <i class="bi bi-person-plus me-2"></i>
                        Đăng ký
                    </router-link>
                </template>
            </div>
        </div>
    </nav>
    
    <router-view></router-view>
</template>

<style scoped>
.navbar {
    padding: 1rem 0;
}

.navbar-brand {
    font-size: 1.5rem;
    font-weight: bold;
    text-decoration: none;
    color: white !important;
}

.navbar-brand:hover {
    color: #0d6efd !important;
}
</style>