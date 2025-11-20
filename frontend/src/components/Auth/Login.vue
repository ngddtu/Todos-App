<script setup>
import { ref } from 'vue';
import api from '@/api';
import { useRouter } from 'vue-router';
const router = useRouter();
const email = ref('');
const password = ref('');
const message = ref('');
const isError = ref(false);

const login = async () => {
    try {
        message.value = '';
        isError.value = false;
        const res = await api.post('/api/login', {
            email: email.value,
            password: password.value
        });
        const token = res.data.access_token;
        const user = res.data.user;

        // Lưu token và user info
        localStorage.setItem('jwt_token', token);
        if (user) {
            localStorage.setItem('user', JSON.stringify(user));
        }

        message.value = 'Đăng nhập thành công!';
        isError.value = false;

        // Redirect sau 1 giây
        setTimeout(() => {
            router.push('/');
        }, 1000);
    } catch (err) {
        message.value = err.response?.data?.message || 'Đăng nhập thất bại';
        isError.value = true;
    }
}
</script>

<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card bg-dark border-secondary shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="text-light mb-3">
                                <i class="bi bi-box-arrow-in-right text-primary me-2"></i>
                                Đăng nhập
                            </h2>
                            <p class="text-muted">Chào mừng bạn trở lại!</p>
                        </div>

                        <!-- Alert Message -->
                        <div v-if="message"
                            :class="['alert', 'alert-dismissible', 'fade', 'show', isError ? 'alert-danger' : 'alert-success']"
                            role="alert">
                            <i :class="isError ? 'bi bi-exclamation-triangle-fill' : 'bi bi-check-circle-fill'"></i>
                            {{ message }}
                            <button type="button" class="btn-close" @click="message = ''"></button>
                        </div>

                        <form @submit.prevent="login">
                            <div class="mb-3">
                                <label for="email" class="form-label text-light">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </label>
                                <input type="email" id="email"
                                    class="form-control form-control-lg bg-dark text-light border-secondary"
                                    v-model="email" placeholder="Nhập email của bạn" required>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label text-light">
                                    <i class="bi bi-lock me-2"></i>Mật khẩu
                                </label>
                                <input type="password" id="password"
                                    class="form-control form-control-lg bg-dark text-light border-secondary"
                                    v-model="password" placeholder="Nhập mật khẩu" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Đăng nhập
                            </button>

                            <div class="text-center">
                                <p class="text-muted mb-0">
                                    Chưa có tài khoản?
                                    <router-link to="/register" class="text-primary text-decoration-none">
                                        Đăng ký ngay
                                    </router-link>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.form-control:focus {
    background-color: #1a1a1a !important;
    border-color: #0d6efd !important;
    color: #fff !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

.card {
    border-radius: 1rem;
}
</style>
