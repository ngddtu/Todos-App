<script setup>
import api from '@/api';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
const router = useRouter();
const newUser = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})
const errors = ref({}); // lưu lỗi từng field
const message = ref('')
const isError = ref(false)

const register = async () => {
    message.value = ''
    isError.value = false
    errors.value = {}
    try {
        const res = await api.post('/api/register', newUser.value)

        if (res.data.success) {
            message.value = res.data.message || 'Đăng ký thành công!'
            isError.value = false

            // Redirect sau 1.5 giây
            setTimeout(() => {
                router.push('/login')
            }, 1500);
        }
    } catch (error) {
        if (error.response?.status === 422) {
            // Backend validation lỗi
            errors.value = error.response.data.errors || {};
            message.value = 'Dữ liệu không hợp lệ';
            isError.value = true;
        } else {
            message.value = error.response?.data?.message || 'Có lỗi xảy ra';
            isError.value = true;
        }
    }
}
</script>

<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card bg-dark border-secondary shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="text-light mb-3">
                                <i class="bi bi-person-plus text-primary me-2"></i>
                                Đăng ký
                            </h2>
                            <p class="text-muted">Tạo tài khoản mới để bắt đầu</p>
                        </div>

                        <!-- Alert Message -->
                        <div v-if="message"
                            :class="['alert', 'alert-dismissible', 'fade', 'show', isError ? 'alert-danger' : 'alert-success']"
                            role="alert">
                            <i :class="isError ? 'bi bi-exclamation-triangle-fill' : 'bi bi-check-circle-fill'"></i>
                            {{ message }}
                            <button type="button" class="btn-close" @click="message = ''"></button>
                        </div>

                        <!-- Validation Errors -->
                        <div v-if="Object.keys(errors).length > 0" class="alert alert-danger">
                            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Lỗi xác thực:</strong>
                            <ul class="mb-0 mt-2">
                                <li v-for="(errorList, field) in errors" :key="field">
                                    <strong>{{ field }}:</strong> {{ errorList.join(', ') }}
                                </li>
                            </ul>
                        </div>

                        <form @submit.prevent="register">
                            <div class="mb-3">
                                <label for="username-register" class="form-label text-light">
                                    <i class="bi bi-person me-2"></i>Tên người dùng
                                </label>
                                <input v-model="newUser.name" id="username-register"
                                    class="form-control form-control-lg bg-dark text-light border-secondary" type="text"
                                    placeholder="Nhập tên người dùng" :class="{ 'is-invalid': errors.name }" required />
                                <div v-if="errors.name" class="invalid-feedback d-block">
                                    {{ errors.name[0] }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email-register" class="form-label text-light">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </label>
                                <input v-model="newUser.email" id="email-register"
                                    class="form-control form-control-lg bg-dark text-light border-secondary"
                                    type="email" placeholder="you@example.com" :class="{ 'is-invalid': errors.email }"
                                    required />
                                <div v-if="errors.email" class="invalid-feedback d-block">
                                    {{ errors.email[0] }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password-register" class="form-label text-light">
                                    <i class="bi bi-lock me-2"></i>Mật khẩu
                                </label>
                                <input v-model="newUser.password" id="password-register"
                                    class="form-control form-control-lg bg-dark text-light border-secondary"
                                    type="password" placeholder="Tạo mật khẩu"
                                    :class="{ 'is-invalid': errors.password }" required />
                                <div v-if="errors.password" class="invalid-feedback d-block">
                                    {{ errors.password[0] }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password-register-confirm" class="form-label text-light">
                                    <i class="bi bi-lock-fill me-2"></i>Xác nhận mật khẩu
                                </label>
                                <input v-model="newUser.password_confirmation" id="password-register-confirm"
                                    class="form-control form-control-lg bg-dark text-light border-secondary"
                                    type="password" placeholder="Xác nhận mật khẩu" required />
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 mb-3">
                                <i class="bi bi-person-plus me-2"></i>
                                Đăng ký
                            </button>

                            <div class="text-center">
                                <p class="text-muted mb-0">
                                    Đã có tài khoản?
                                    <router-link to="/login" class="text-primary text-decoration-none">
                                        Đăng nhập ngay
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

.form-control.is-invalid {
    border-color: #dc3545 !important;
}

.card {
    border-radius: 1rem;
}
</style>