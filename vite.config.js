import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/sub-product-filter.js',
                'resources/js/license-filter.js',
                'resources/js/admin-download-filter.js'
            ],
            refresh: true,
        }),
    ],
});
