import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Lấy CSRF token từ meta tag trong HTML
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
// Kiểm tra nếu CSRF token tồn tại và thêm vào header của Axios
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

import './echo';