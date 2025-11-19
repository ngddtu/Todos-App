import axios from 'axios';


// axios.defaults.withCredentials = true;
// axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
// axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL, // Lấy giá trị từ .env
  // withCredentials: true, // BẮT BUỘC

});
api.interceptors.request.use(config => {
  const token = localStorage.getItem('jwt_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});


export default api;
