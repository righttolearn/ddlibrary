import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import * as path from "node:path";
import rtlcss from 'postcss-rtlcss';

export default defineConfig({
    server: {
        host: '0.0.0.0',
    },
    css: {
        postcss: {
            plugins: [rtlcss()]
        }
    },
    plugins: [
        laravel({
            input: [
                'resources/assets/sass/app.scss',
                'resources/assets/js/app.jsx',
                'resources/assets/js/epub.jsx',
                'resources/assets/js/tinymce.js',
                'resources/assets/js/modules/resource_form.jsx',
                'resources/assets/js/modules/image_manager.jsx',
                'resources/assets/js/modules/resource_filter.jsx',
                'resources/assets/js/modules/glossary.jsx',
                'resources/assets/js/modules/resource_view.jsx',
                'resources/assets/js/modules/auth.jsx',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '../webfonts': path.resolve(__dirname, 'node_modules/@fortawesome/fontawesome-free/webfonts'),
        },
    },
    optimizeDeps: {
        include: ['jquery'],
    },
    define: {
        global: 'window',
    },
});
