import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import axios from 'axios'; // Import axios để kiểm tra session
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { createApp, h } from 'vue';
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import helpers from './lib/helpers';
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
import JSZip from 'jszip'
import VueLazyLoad from 'vue3-lazyload'

const pinia = createPinia()

// Prime Vue
import PrimeVue from 'primevue/config';

// Cấu hình tùy chọn cho Toast
const options = {
    autoClose: 3000,  // Thời gian toast tự động đóng (3 giây)
    position: "top-right",  // Vị trí của toast
};
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VueLazyLoad)
            .use(ZiggyVue)
            .use(Vue3Toastify, options)
            .use(PrimeVue, {
                // Default theme configuration
                theme: {
                    // preset: Aura,
                    options: {
                        prefix: 'p',
                        darkModeSelector: 'system',
                        cssLayer: false
                    }
                }
            })
            .use(pinia)
            .use({
                install(app) {
                    app.config.globalProperties.$helpers = helpers
                    app.provide('helpers', helpers)
                },
            })
            .use(JSZip)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
axios.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        // Kiểm tra nếu lỗi là 401
        if (error.response && error.response.status === 401) {
            window.location.href = '/login';
        }

        if (error.response && error.response.status === 405) {
            window.location.href = '/error/permission';
        }
        // Nếu không phải lỗi 401, tiếp tục trả lỗi về
        return Promise.reject(error);
    }
);
