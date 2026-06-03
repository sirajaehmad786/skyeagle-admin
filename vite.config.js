import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

/**
 * Vite `base` must match the public URL path (e.g. /admin/ in production).
 * Uses VITE_BASE when set, otherwise derives from APP_URL pathname.
 */
function resolveViteBase(env) {
    const explicit = (env.VITE_BASE || '').trim();
    if (explicit !== '') {
        return explicit.endsWith('/') ? explicit : `${explicit}/`;
    }

    const appUrl = (env.APP_URL || '').trim();
    if (appUrl === '') {
        return null;
    }

    try {
        const pathname = new URL(appUrl).pathname.replace(/\/$/, '');
        if (pathname && pathname !== '/') {
            // Must include /build/ so icon fonts resolve to /admin/build/assets/...
            return `${pathname}/build/`;
        }
    } catch {
        // invalid APP_URL
    }

    return null;
}

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const base = resolveViteBase(env);

    return {
    ...(base ? { base } : {}),
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/scss/app.scss',
                'resources/scss/icons.scss',

                'node_modules/daterangepicker/daterangepicker.css',
                'node_modules/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css',
                'node_modules/fullcalendar/main.min.css',
                'node_modules/quill/dist/quill.core.css',
                'node_modules/quill/dist/quill.snow.css',
                'node_modules/quill/dist/quill.bubble.css',
                'node_modules/jquery-toast-plugin/dist/jquery.toast.min.css',
                'node_modules/select2/dist/css/select2.min.css',
                'node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css',
                'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
                'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
                'node_modules/flatpickr/dist/flatpickr.min.css',
                'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
                'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
                'node_modules/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css',
                'node_modules/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css',
                'node_modules/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css',
                'node_modules/datatables.net-select-bs5/css/select.bootstrap5.min.css',
                'node_modules/dropzone/dist/dropzone.css',


                // My css
                'resources/css/crm/custom.css',

                'resources/js/bootstrap.js',
                'resources/js/app.js',
                'resources/js/head.js',
                'resources/js/layout.js',
                'resources/js/pages/demo.toastr.js',
                'resources/js/pages/component.chat.js',
                'resources/js/pages/demo.apex-mixed.js',
                'resources/js/pages/demo.apex-timeline.js',
                'resources/js/pages/demo.apex-pie.js',
                'resources/js/pages/demo.apex-radialbar.js',
                'resources/js/pages/demo.apex-sparklines.js',

                'node_modules/jquery/dist/jquery.js',
                'node_modules/daterangepicker/moment.min.js',
                'node_modules/dragula/dist/dragula.min.js',
                'node_modules/jquery-toast-plugin/dist/jquery.toast.min.js',
                'node_modules/jquery.rateit/scripts/jquery.rateit.min.js',

                'node_modules/datatables.net/js/jquery.dataTables.min.js',
                'node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
                'node_modules/datatables.net-responsive/js/dataTables.responsive.min.js',
                'node_modules/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
                'node_modules/jquery-datatables-checkboxes/js/dataTables.checkboxes.min.js',
                'node_modules/datatables.net-buttons/js/dataTables.buttons.min.js',
                'node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js',
                'node_modules/datatables.net-buttons/js/buttons.html5.min.js',
                'node_modules/datatables.net-buttons/js/buttons.flash.min.js',
                'node_modules/datatables.net-buttons/js/buttons.print.min.js',
                'node_modules/datatables.net-keytable/js/dataTables.keyTable.min.js',
                'node_modules/datatables.net-select/js/dataTables.select.min.js',
                'node_modules/select2/dist/js/select2.min.js',
                'node_modules/jquery-toast-plugin/src/jquery.toast.js',

                'typeahead.js/dist/typeahead.bundle.js',
                'typeahead.js/dist/typeahead.bundle.min.js',
                'jquery.rateit/scripts/jquery.rateit.js',

                // Dashboard js
                'resources/js/pages/demo.dashboard.js',
                'resources/js/pages/demo.dashboard-analytics.js',
                'resources/js/crm/dashboard/index.js',

                // Apps js
                'resources/js/pages/demo.calendar.js',
                'resources/js/pages/component.dragula.js',
                'resources/js/pages/component.dragula.js',

                // Email js
                'resources/js/pages/demo.inbox.js',

                // Pages
                'resources/js/pages/demo.profile.js',

                // Task js
                'resources/js/pages/demo.tasks.js',
                'resources/js/pages/component.fileupload.js',

                // Extended ui js
                'resources/js/pages/component.rating.js',
                'resources/js/pages/component.dragula.js',
                'resources/js/pages/component.range-slider.js',
                'resources/js/pages/component.rating.js',

                // Widgets js
                'resources/js/pages/demo.widgets.js',
                'resources/js/pages/component.todo.js',

                // Icons js
                'resources/js/pages/demo.bootstrapicons.js',
                'resources/js/pages/demo.remixicons.js',


                // Apex Chart
                'resources/js/pages/demo.apex-area.js',
                'resources/js/pages/demo.apex-bar.js',
                'resources/js/pages/demo.apex-boxplot.js',
                'resources/js/pages/demo.apex-bubble.js',
                'resources/js/pages/demo.apex-candlestick.js',
                'resources/js/pages/demo.apex-column.js',
                'resources/js/pages/demo.apex-heatmap.js',
                'resources/js/pages/demo.apex-line.js',
                'resources/js/pages/demo.apex-heatmap.js',
                'resources/js/pages/demo.apex-heatmap.js',
                'resources/js/pages/demo.apex-polar-area.js',
                'resources/js/pages/demo.apex-radar.js',
                'resources/js/pages/demo.apex-scatter.js',
                'resources/js/pages/demo.apex-scatter.js',
                'resources/js/pages/demo.apex-scatter.js',
                'resources/js/pages/demo.apex-treemap.js',

                // Charts Js
                'resources/js/pages/demo.chartjs-area.js',
                'resources/js/pages/demo.chartjs-bar.js',
                'resources/js/pages/demo.chartjs-line.js',
                'resources/js/pages/demo.chartjs-other.js',

                // Forms Js
                'resources/js/pages/demo.form-advanced.js',

                // 'resources/js/pages/demo.timepicker.js',
                'resources/js/pages/demo.quilljs.js',
                'resources/js/pages/component.fileupload.js',
                'resources/js/pages/demo.form-wizard.js',

                // Tables js
                'resources/js/pages/demo.datatable-init.js',

                // Maps Js
                'resources/js/pages/demo.google-maps.js',
                'resources/js/pages/demo.vector-maps.js',

                'node_modules/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js',
                'node_modules/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js',
                'bootstrap-timepicker/js/bootstrap-timepicker.min.js',
                'node_modules/jquery-mask-plugin/dist/jquery.mask.min.js',

                //My custom JS
                'resources/js/crm/common/common.js',
                'resources/js/crm/common/form-handler.js',
                'resources/js/crm/common/datatable-setup.js',
                
                //Contact js
                'resources/js/crm/contact/index.js',
                'resources/js/crm/contact/edit.js',
                'resources/js/crm/contact/contact-assign.js',

                //Contact js
                'resources/js/crm/user/index.js',

                //Leads js
                'resources/js/crm/lead/index.js',
                'resources/js/crm/lead/edit.js',

                //Quotation js
                'resources/js/crm/quotation/index.js',
                'resources/js/crm/quotation/create.js',
                'resources/js/crm/quotation/edit.js',
                'resources/js/crm/quotation/flight.js',
                'resources/js/crm/quotation/visa.js',
                'resources/js/crm/quotation/sightseeing.js',
                'resources/js/crm/quotation/hotel.js',
                'resources/js/crm/quotation/pdf_generate.js',

                //Hotels Master
                'resources/js/crm/hotel/create.js',
                'resources/js/crm/hotel/edit.js',
                'resources/js/crm/hotel/index.js',

                //Sightseeing master
                'resources/js/crm/sightseeing/index.js',
                'resources/js/crm/sightseeing/edit.js',
                'resources/js/crm/sightseeing/create.js',

                //Booking
                'resources/js/crm/booking/index.js',

                //Payment
                'resources/js/crm/payment/index.js',
                'resources/js/crm/payment/paymentHistory.js',
                'resources/js/crm/common/payment-modal.js',

                //Profile
                'resources/js/crm/profile/profile.js',

                //notification
                'resources/js/crm/notification/index.js',
                'resources/js/crm/notification/global-notification.js',

                //Settings
                'resources/js/crm/setting/create.js',
                
                //Document  
                'resources/js/crm/document/index.js',
                'resources/js/crm/document/edit.js',
                'resources/js/crm/document/create.js',

                //Activity
                'resources/js/crm/activity/index.js',

                //Booking Document
                'resources/js/crm/bookingDocument/create.js',


                //skyeagle website

                // login
                'resources/js/auth/login.js',

                //category
                'resources/js/crm/category/create.js',
                'resources/js/crm/category/edit.js',
                'resources/js/crm/category/index.js',

                //package
                'resources/js/crm/package/create.js',
                'resources/js/crm/package/edit.js',
                'resources/js/crm/package/index.js',

                //enquiry
                'resources/js/crm/enquiry/index.js',

                
                //media
                'resources/js/crm/media/index.js',
                'resources/js/crm/media/create.js',
                'resources/js/crm/media/edit.js',

                //CustomerReview
                'resources/js/crm/customerReview/index.js',
                'resources/js/crm/customerReview/create.js',
                
            ],
            refresh: true,
        }),
    ],
    build: {
        sourcemap: false,
    },
    resolve: {
        alias: {
            $: "jQuery",
        },
    },
    };
});
