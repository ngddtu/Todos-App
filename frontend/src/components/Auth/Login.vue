<script setup>
import { ref } from 'vue';
import api from '@/api';
import { useRouter } from 'vue-router';
const router = useRouter();
const email = ref('');
const password = ref('');
const message = ref('');

const login = async () => {
    try {
        const res = await api.post('/api/login', { email: email.value, password: password.value });
        const token = res.data.access_token;
        localStorage.setItem('jwt_token', token);
        message.value = 'Login thành công';
        console.log(res.data);
        router.push('/');
    } catch (err) {
        message.value = err.response?.data?.message || err.message;
    }
}
</script>

<template>
    <div>
        <input v-model="email" placeholder="Email">
        <input v-model="password" placeholder="Password" type="password">
        <button @click="login">Login</button>
        <p>{{ message }}</p>
    </div>
</template>
