import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { initializeTheme } from './composables/useAppearance'

createInertiaApp({
    resolve: (name: string) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`] as any
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
// This will set light / dark mode on page load...
initializeTheme();
