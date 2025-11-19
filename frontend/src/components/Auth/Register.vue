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
            message.value = res.data.message
            isError.value = false
            
        }
        console.log(res.data);
        router.push('/login')
    } catch (error) {
        if (error.response?.status === 422) {
            // Backend validation lỗi
            errors.value = error.response.data.errors;
            isError.value = true;
        } else {
            message.value = 'Có lỗi xảy ra';
            isError.value = true;
        }
    }
}
</script>
<template>
    <div class="col-lg-5 pl-lg-5 pb-3 py-lg-5">
        <div v-if="message" :class="['alert', isError ? 'alert-danger' : 'alert-success']">
            {{ message }}
        </div>
        <form @submit.prevent="register">
            <div class="form-group">
                <label for="username-register" class="text-muted mb-1"><small>Username</small></label>
                <input v-model="newUser.name" id="username-register" class="form-control" type="text"
                    placeholder="Pick a username" autocomplete="off" />
                <small v-if="errors.name" class="text-danger">{{ errors.name[0] }}</small>
            </div>

            <div class="form-group">
                <label for="email-register" class="text-muted mb-1"><small>Email</small></label>
                <input v-model="newUser.email" id="email-register" class="form-control" type="text"
                    placeholder="you@example.com" autocomplete="off" />
                <small v-if="errors.email" class="text-danger">{{ errors.email[0] }}</small>
            </div>

            <div class="form-group">
                <label for="password-register" class="text-muted mb-1"><small>Password</small></label>
                <input v-model="newUser.password" id="password-register" class="form-control" type="password"
                    placeholder="Create a password" />
                <small v-if="errors.password" class="text-danger">{{ errors.password[0] }}</small>
            </div>

            <div class="form-group">
                <label for="password-register-confirm" class="text-muted mb-1"><small>Confirm Password</small></label>
                <input v-model="newUser.password_confirmation" id="password-register-confirm" class="form-control"
                    type="password" placeholder="Confirm password" />
            </div>

            <button type="submit" class="py-3 mt-4 btn btn-lg btn-success btn-block">Sign up for OurApp</button>
        </form>
    </div>
</template>