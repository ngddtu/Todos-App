<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <!-- Header -->
        <div class="text-center mb-5">
          <h1 class="display-4 fw-bold text-light mb-3">
            <i class="bi bi-list-check text-primary"></i> Todo App
          </h1>
          <p class="text-muted">Quản lý công việc hôm nay</p>
        </div>

        <!-- Add Todo Form -->
        <div class="card bg-dark border-secondary mb-4 shadow-lg">
          <div class="card-body p-4">
            <form @submit.prevent="submitTodo" class="mb-0">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label for="todo-title" class="form-label text-light">
                    <i class="bi bi-pencil-square me-2"></i>Tiêu đề
                  </label>
                  <input type="text" id="todo-title"
                    class="form-control form-control-lg bg-dark text-light border-secondary" v-model="todo.title"
                    placeholder="Nhập tiêu đề công việc..." required>
                </div>
                <div class="col-12 col-md-4">
                  <label for="todo-deadline" class="form-label text-light">
                    <i class="bi bi-calendar-event me-2"></i>Hạn chót
                  </label>
                  <input type="date" id="todo-deadline"
                    class="form-control form-control-lg bg-dark text-light border-secondary" v-model="todo.deadline"
                    required>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                  <button class="btn btn-primary btn-lg w-150" type="submit">
                    <i class="bi bi-plus-circle me-2"></i>Thêm
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Filter and Stats -->
        <div class="card bg-dark border-secondary mb-4 shadow-sm">
          <div class="card-body p-3">
            <div class="row g-3 align-items-center">
              <!-- Time Filter Select -->
              <div class="col-12 col-md-4">
                <label for="time-filter" class="form-label text-light mb-2">
                  <i class="bi bi-funnel me-2"></i>Lọc theo thời gian
                </label>
                <select id="time-filter" v-model="timeFilter" @change="filterTodos"
                  class="form-select bg-dark text-light border-secondary">
                  <option value="alltime">Tất cả</option>
                  <option value="today">Hôm nay</option>
                  <option value="yesterday">Hôm qua</option>
                  <option value="this_week">Tuần này</option>
                </select>
              </div>
              <!-- Per page -->
              <div class="col-12 col-md-2">
                <label for="per-page" class="form-label text-light mb-2">
                  <i class="bi bi-list-ol me-2"></i>Hiển thị
                </label>
                <select id="per-page" v-model.number="perPage" @change="changePerPage"
                  class="form-select bg-dark text-light border-secondary">
                  <option :value="5">5 / trang</option>
                  <option :value="10">10 / trang</option>
                  <option :value="15">15 / trang</option>
                </select>
              </div>

              <!-- Status Stats -->
              <div class="col-12 col-md-6">
                <label class="form-label text-light mb-2">
                  <i class="bi bi-bar-chart me-2"></i>Thống kê
                </label>
                <div class="d-flex gap-2 flex-wrap">
                  <button type="button" class="btn btn-outline-warning flex-fill"
                    :class="{ 'active': statusFilter === 'pending' }" @click="filterByStatus('pending')">
                    <i class="bi bi-circle me-2"></i>
                    Chưa hoàn thành
                    <span class="badge bg-warning text-dark ms-2">{{ stats.pending }}</span>
                  </button>
                  <button type="button" class="btn btn-outline-success flex-fill"
                    :class="{ 'active': statusFilter === 'completed' }" @click="filterByStatus('completed')">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Đã hoàn thành
                    <span class="badge bg-success ms-2">{{ stats.completed }}</span>
                  </button>
                  <button type="button" class="btn btn-outline-info flex-fill"
                    :class="{ 'active': statusFilter === 'all' }" @click="filterByStatus('all')">
                    <i class="bi bi-list-ul me-2"></i>
                    Tất cả
                    <span class="badge bg-info text-dark ms-2">{{ stats.total }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Alert Messages -->
        <div v-if="message"
          :class="['alert', 'alert-dismissible', 'fade', 'show', isError ? 'alert-danger' : 'alert-success']"
          role="alert">
          <i :class="isError ? 'bi bi-exclamation-triangle-fill' : 'bi bi-check-circle-fill'"></i>
          {{ message }}
          <button type="button" class="btn-close" @click="message = ''"></button>
        </div>

        <!-- Validation Errors -->
        <div v-if="Object.keys(errors).length > 0" class="alert alert-danger alert-dismissible fade show">
          <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Lỗi xác thực:</strong>
          <ul class="mb-0 mt-2">
            <li v-for="(errorList, field) in errors" :key="field">
              <strong>{{ field }}:</strong> {{ errorList.join(', ') }}
            </li>
          </ul>
          <button type="button" class="btn-close" @click="errors = {}"></button>
        </div>

        <!-- Todo List -->
        <div v-if="todosToday.length === 0" class="text-center py-5">
          <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
          <p class="text-muted fs-5">Chưa có công việc nào hôm nay</p>
        </div>

        <div v-else class="todo-list">
          <div v-for="(item, index) in todosToday" :key="item.id"
            class="card bg-dark border-secondary mb-3 shadow-sm hover-card">
            <!-- Edit Mode -->
            <div v-if="item.isEditing" class="card-body p-4">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label text-light">Tiêu đề</label>
                  <input type="text" class="form-control bg-dark text-light border-secondary" v-model="item.editTitle">
                </div>
                <div class="col-12 col-md-4">
                  <label class="form-label text-light">Hạn chót</label>
                  <input type="date" class="form-control bg-dark text-light border-secondary"
                    v-model="item.editDeadline">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2 align-items-end">
                  <button type="button" class="btn btn-success flex-fill" @click.prevent="updateTodo(item)">
                    <i class="bi bi-check-lg"></i>
                  </button>
                  <button type="button" class="btn btn-secondary flex-fill" @click.prevent="cancelEdit(item)">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- View Mode -->
            <div v-else class="card-body p-4" :class="{ 'todo-completed': item.is_done }">
              <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                  <h5 class="card-title text-light mb-2 d-flex align-items-center">

                    <button type="button" class="btn-checkbox me-2 p-0 border-0 bg-transparent"
                      @click.prevent="toggleTodoStatus(item)"
                      :title="item.is_done === 1 ? 'Đánh dấu chưa hoàn thành' : 'Đánh dấu hoàn thành'">
                      <i :class="[
                        'fs-5',
                        item.is_done === 1 ? 'bi-check-circle-fill text-success' : 'bi-circle text-primary'
                      ]" style="cursor: pointer; transition: all 0.3s ease;"></i>
                    </button>

                    <span :class="{ 'text-decoration-line-through text-muted': item.is_done === 1 }">
                      {{ item.title }}
                    </span>
                  </h5>
                  <div class="text-muted small">
                    <div class="mb-1">
                      <i class="bi bi-calendar-event me-2"></i>
                      Hạn chót: <strong>{{ formatDate(item.deadline) }}</strong>
                    </div>
                    <div>
                      <i class="bi bi-clock-history me-2"></i>
                      Tạo: {{ formatDate(item.created_at) }}
                    </div>
                  </div>
                </div>
                <div class="d-flex gap-2 ms-3">
                  <button type="button" class="btn btn-outline-primary btn-sm" @click.prevent="editTodo(item)"
                    title="Sửa">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-sm" @click.prevent="confirmDelete(item)"
                    title="Xóa">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>


        </div>
        <!-- Pagination -->
        <nav class="d-flex justify-content-between align-items-center mt-4" aria-label="Phân trang">
          <div class="text-muted small">
            Trang {{ currentPage }} / {{ lastPage }} • Tổng {{ totalTodos }} công việc
          </div>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <button class="page-link" type="button" @click="changePage(currentPage - 1)"
                :disabled="currentPage === 1">
                Trước
              </button>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === lastPage }">
              <button class="page-link" type="button" @click="changePage(currentPage + 1)"
                :disabled="currentPage === lastPage">
                Sau
              </button>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import api from '@/api';
// import axios from 'axios';
import { ref, onMounted } from 'vue';
// import { useRouter } from 'vue-router';

// const editingId = ref(null)
const todosToday = ref([])
const errors = ref({}); // lưu lỗi từng field
const message = ref('')
const isError = ref(false)
const timeFilter = ref('') // today, yesterday, this_week
const statusFilter = ref('all') // all, pending, completed
const stats = ref({
  total: 0,
  completed: 0,
  pending: 0
})
const currentPage = ref(1)
const lastPage = ref(1)
const totalTodos = ref(0)
const perPage = ref(5)



const todo = ref({
  title: '',
  deadline: '',
})

const submitTodo = async () => {
  try {
    const res = await api.post('/api/todos/store', todo.value)

    if (res.data.success) {
      message.value = 'Thêm công việc thành công!';
      isError.value = false;
      // Reset form
      todo.value = {
        title: '',
        deadline: ''
      };
      errors.value = {};
      // Lấy lại danh sách todo sau khi thêm thành công
      await getTodosToday1();
      // Auto hide message sau 3 giây
      setTimeout(() => {
        message.value = '';
      }, 3000);
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



const getTodosToday1 = async () => {
  try {
    const params = new URLSearchParams({
      time_filter: timeFilter.value,
      page: currentPage.value.toString(),
      per_page: perPage.value.toString()
    });

    if (statusFilter.value !== 'all') {
      params.append('status', statusFilter.value);
    }

    const res = await api.get(`/api/todos?${params.toString()}`);

    // Cập nhật stats từ response
    if (res.data.stats) {
      stats.value = res.data.stats;
    }
    if (res.data.pagination) {
      currentPage.value = res.data.pagination.current_page;
      lastPage.value = res.data.pagination.last_page;
      totalTodos.value = res.data.pagination.total;
    } else {
      lastPage.value = 1;
      totalTodos.value = res.data.todos?.length || 0;
    }

    // Map todos với các thuộc tính cần thiết
    todosToday.value = (res.data.todos || res.data).map(t => ({
      ...t,
      // Đảm bảo is_done là số nguyên (1 hoặc 0)
      is_done: t.is_done ? 1 : 0,
      isEditing: false,
      editTitle: t.title,
      editDeadline: t.deadline
    }));
  } catch (error) {
    console.error('Get todos error:', error.response?.data || error.message);
    message.value = 'Có lỗi xảy ra khi tải danh sách';
    isError.value = true;
  }
};

// Filter theo thời gian
const filterTodos = () => {
  currentPage.value = 1;
  getTodosToday1();
}

// Filter theo trạng thái
const filterByStatus = (status) => {
  statusFilter.value = status;
  currentPage.value = 1;
  getTodosToday1();
}

const changePage = (page) => {
  if (page < 1 || page > lastPage.value) return;
  currentPage.value = page;
  getTodosToday1();
}

const changePerPage = () => {
  currentPage.value = 1;
  getTodosToday1();
}

// Load danh sách khi component mount
// onMounted(() => {
//   getTodosToday1();
// })

const editTodo = (item) => {
  item.isEditing = true;
}

const cancelEdit = (item) => {
  item.isEditing = false;
  // Reset về giá trị ban đầu
  item.editTitle = item.title;
  item.editDeadline = item.deadline;
};


const updateTodo = async (item) => {
  try {
    console.log('Updating todo:', item.id, { title: item.editTitle, deadline: item.editDeadline });
    const res = await api.put(`/api/todos/${item.id}`, {
      title: item.editTitle,
      deadline: item.editDeadline
    });

    console.log('Update response:', res.data);
    if (res.data.success) {
      item.title = item.editTitle;
      item.deadline = item.editDeadline;
      item.isEditing = false;
      message.value = res.data.message || 'Cập nhật thành công';
      isError.value = false;
      // Reload danh sách sau khi update
      await getTodosToday1();
    }
  } catch (error) {
    console.error('Update error:', error.response?.data || error.message);
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {};
      message.value = error.response.data.message || 'Dữ liệu không hợp lệ';
      isError.value = true;
    } else {
      message.value = error.response?.data?.message || 'Có lỗi xảy ra khi cập nhật';
      isError.value = true;
    }
  }
}

const deleteTodo = async (id) => {
  try {
    console.log('Deleting todo:', id);
    const res = await api.delete(`/api/todos/${id}`);
    console.log('Delete response:', res.data);
    if (res.data.success) {
      message.value = res.data.message || 'Xóa thành công';
      isError.value = false;
      return true;
    }
    return false;
  } catch (error) {
    console.error('Delete error:', error.response?.data || error.message);
    message.value = error.response?.data?.message || 'Có lỗi xảy ra khi xóa';
    isError.value = true;
    return false;
  }
}

const confirmDelete = async (item) => {
  if (window.confirm('Bạn có chắc muốn xóa công việc này?')) {
    const success = await deleteTodo(item.id);
    if (success) {
      await getTodosToday1();
    }
  }
}

// Toggle todo status (completed/uncompleted)
const toggleTodoStatus = async (item) => {
  try {
    // Chuyển đổi giá trị hiện tại: nếu là 1 thì thành 0, nếu là 0 thì thành 1
    const currentStatus = item.is_done ? 1 : 0;
    const newStatus = currentStatus === 1 ? 0 : 1;

    console.log('Toggling status:', { id: item.id, current: currentStatus, new: newStatus });

    const res = await api.put(`/api/todos/${item.id}`, {
      is_done: newStatus
    });

    if (res.data.success) {
      // Cập nhật trạng thái local
      item.is_done = newStatus;
      message.value = newStatus === 1 ? 'Đã đánh dấu hoàn thành!' : 'Đã bỏ đánh dấu hoàn thành';
      isError.value = false;
      // Reload danh sách để cập nhật stats
      await getTodosToday1();
      // Auto hide message sau 2 giây
      setTimeout(() => {
        message.value = '';
      }, 2000);
    }
  } catch (error) {
    console.error('Toggle status error:', error.response?.data || error.message);
    message.value = error.response?.data?.message || 'Có lỗi xảy ra khi cập nhật trạng thái';
    isError.value = true;
  }
}

// Format date helper
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  });
}
</script>

<style scoped>
.hover-card {
  transition: all 0.3s ease;
  border-width: 2px;
}

.hover-card:hover {
  transform: translateY(-2px);
  border-color: rgba(13, 110, 253, 0.5) !important;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
}

.todo-list {
  max-height: 70vh;
  overflow-y: auto;
}

/* Custom scrollbar for dark theme */
.todo-list::-webkit-scrollbar {
  width: 8px;
}

.todo-list::-webkit-scrollbar-track {
  background: #1a1a1a;
  border-radius: 4px;
}

.todo-list::-webkit-scrollbar-thumb {
  background: #495057;
  border-radius: 4px;
}

.todo-list::-webkit-scrollbar-thumb:hover {
  background: #6c757d;
}

/* Form controls dark theme */
.form-control:focus,
.form-control:active {
  background-color: #1a1a1a !important;
  border-color: #0d6efd !important;
  color: #fff !important;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

/* Card styling */
.card {
  transition: all 0.3s ease;
}

/* Button hover effects */
.btn {
  transition: all 0.2s ease;
}

.btn:hover {
  transform: scale(1.05);
}

.btn-sm {
  padding: 0.375rem 0.75rem;
}

/* Alert styling */
.alert {
  border: none;
  border-radius: 0.5rem;
}

/* Todo completed state */
.todo-completed {
  opacity: 0.7;
}

.todo-completed .card-title {
  opacity: 0.8;
}

.btn-checkbox {
  cursor: pointer;
  transition: transform 0.2s ease;
}

.btn-checkbox:hover {
  transform: scale(1.2);
}

.btn-checkbox:active {
  transform: scale(0.9);
}

/* Completed todo styling */
.text-decoration-line-through {
  text-decoration: line-through !important;
}

/* Filter buttons active state */
.btn.active {
  background-color: var(--bs-primary) !important;
  border-color: var(--bs-primary) !important;
  color: white !important;
}

.btn-outline-warning.active {
  background-color: var(--bs-warning) !important;
  border-color: var(--bs-warning) !important;
  color: #000 !important;
}

.btn-outline-success.active {
  background-color: var(--bs-success) !important;
  border-color: var(--bs-success) !important;
  color: white !important;
}

.btn-outline-info.active {
  background-color: var(--bs-info) !important;
  border-color: var(--bs-info) !important;
  color: #000 !important;
}

/* Select styling for dark theme */
.form-select:focus {
  background-color: #1a1a1a !important;
  border-color: #0d6efd !important;
  color: #fff !important;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .container {
    padding-left: 15px;
    padding-right: 15px;
  }

  .display-4 {
    font-size: 2rem;
  }

  .btn {
    font-size: 0.875rem;
    padding: 0.5rem;
  }
}
</style>