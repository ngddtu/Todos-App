<script setup></script>

<template>
   <router-link :to="{name:'form-login'}">Đăng nhập</router-link>
  <router-link :to="{name:'form-register'}">Đăng ký</router-link>
  <router-view ></router-view>
</template>