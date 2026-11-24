import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/storefront.css', 'resources/js/storefront.js', 'resources/css/catalog.css'],
            refresh: true,
        }),
    ],
});
