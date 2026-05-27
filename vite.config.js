import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import * as path from "node:path";

export default defineConfig({
    server: {
        host: '0.0.0.0',
    },
    plugins: [
        laravel({
            input: [
                'resources/assets/sass/app.scss',
                'resources/assets/js/ddl.jsx',
                'resources/assets/js/epub.jsx',
                'resources/assets/js/tinymce.js',
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
