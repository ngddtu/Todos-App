<template>
  <form @submit.prevent="submitTodo">  
    <input type="text" v-model="todo.title">
    <input type="date" v-model="todo.deadline">
    <button class="btn btn-info" type="submit">Thêm</button>
  </form>
  <!-- <input type="checkbox"></input> -->
  <!-- <form @submit.prevent="getTodos">
    <button type="submit">Lấy danh sách</button>
  </form> -->
</template>

<script setup>
  import api from '@/api';
  import { ref } from 'vue';
  import { onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  const router = useRouter();
  // const isNone = ref(false)
  // const satus = ref('0')
  const errors = ref({}); // lưu lỗi từng field
  const message = ref('')
  const isError = ref(false)  
  const todo = ref({
    title: '',
    deadline: '',
  })
  const submitTodo = async () => {
    try{
      const res = await api.post('/api/todos/store', todo.value)
      // console.log(todo.value);
      
      if (res.data.success) {
            message.value = res.data.message
            isError.value = false
            const newTodos = await api.get('/api/todos')
            const data = newTodos.data
            console.log(data);
            
        }
        console.log(res.data);
        // router.push('/login')
    } catch (error){
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

  // const getTodos = async () => {
  //   const res = await api.get('/api/todos')
  //   const data = res.data

  //   console.log(data);
    
  // }
  
</script>

<style scoped>

</style>