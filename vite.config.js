import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                'resources/views/**',
                'app/**',
                'routes/**',
                'config/**',
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: false,
        watch: {
            usePolling: false,
            ignored: [
                '**/storage/framework/views/**',
                '**/node_modules/**',
                '**/vendor/**',
            ],
        },
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
    },
});
