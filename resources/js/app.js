import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { useSmoothScroll } from './composables/useSmoothScroll';
import CustomAlert from './Components/CustomAlert.vue';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({
            render: () => [
                h(App, props),
                h(CustomAlert),
            ],
        })
            .use(plugin)
            .mount(el);

        // Set up once for the whole SPA rather than per page, so the glide is
        // not torn down and rebuilt on every Inertia visit.
        useSmoothScroll();

        // The <title> is printed by app.blade.php, which only runs on a full
        // page load — a crawler always gets the right one, but a visitor
        // clicking through the SPA kept the title of the page they came from.
        // Every controller already passes `meta`, so the tab follows along.
        const applyTitle = (page) => {
            const title = page?.props?.meta?.title;

            if (title) document.title = title;
        };

        router.on('success', event => applyTitle(event.detail.page));
    },
});
