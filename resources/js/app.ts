import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { configureEcho } from '@laravel/echo-vue';

console.log(import.meta.env)

configureEcho({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    enabledTransports: ['ws'],

     withCredentials: true, // 👈 ESTO

    authEndpoint: '/broadcasting/auth',

});



// import '../css/app.css';

router.on('error', (event) => {
    const errors = event.detail?.errors;
    if (!errors) return;

    const message = Object.values(errors)[0];
    if (message) {
        toast.error(message);
    }
});

router.on('flash', (event) => {
    const flash = event.detail.flash;
    if (flash?.success) {
        toast.success(flash.success);
    }

    if (flash?.error) {
        toast.error(flash.error);
    }
});

createInertiaApp({
    resolve: (name: string) => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true });
        return pages[`./pages/${name}.vue`] as any;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
// This will set light / dark mode on page load...
initializeTheme();
