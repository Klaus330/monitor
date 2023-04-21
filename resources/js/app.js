import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Link, Head } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import AppLayout from './Layouts/AppLayout.vue';
import HighChartsVue from 'highcharts-vue'
import TimelineModule from 'highcharts/modules/timeline'
import AccessibilityModule from 'highcharts/modules/accessibility'
import DebugModule from 'highcharts/modules/debugger'
import HighChart from 'highcharts'
import highchartsMore from 'highcharts/highcharts-more'
TimelineModule(HighChart)
DebugModule(HighChart)
AccessibilityModule(HighChart)
highchartsMore(HighChart)

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => { 
        const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')) 

        page.then((module) => {
            if(module.default.layout === undefined) {
                module.default.layout =  AppLayout;
            }
        })

        return page
    },
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(HighChartsVue)
            .component("Link", Link)
            .component("Head", Head)
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
