import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue')
        );

        page.then((module) => {
            if (module.default.layout === undefined) {
                switch (true) {
                    case name === 'Welcome':
                        module.default.layout = null;
                        break;
                    case name.startsWith('auth/'):
                        module.default.layout = AuthLayout;
                        break;
                    case name.startsWith('settings/'):
                        module.default.layout = [AppLayout, SettingsLayout];
                        break;
                    default:
                        module.default.layout = AppLayout;
                }
            }
        });

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#A66353',
    },
});

initializeTheme();
initializeFlashToast();