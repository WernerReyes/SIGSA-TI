import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

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
