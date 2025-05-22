import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/login.js',
                'resources/js/dashboard/dashboard.js',
                'resources/js/stores.js',
                'resources/js/subtypes.js',
            ],
            refresh: true,
        }),
    ],
});
