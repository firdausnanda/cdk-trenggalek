import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import path from 'path';
// import { fileURLToPath } from 'url';

// const __filename = fileURLToPath(import.meta.url);
// const __dirname = path.dirname(__filename);

export default defineConfig({
    build: {
        outDir: '../../public/build-useravatar',
        emptyOutDir: true,
        manifest: true,
    },
    plugins: [
        laravel({
            publicDirectory: '../../public',
            buildDirectory: 'build-useravatar',
            input: [
                'resources/assets/js/app.js',
                'resources/assets/css/app.css',
            ],
            refresh: true,
        }),
    ],
    paths: [
        'resources/assets/js/app.js',
        'resources/assets/css/app.css',
    ],
    esbuild: {
        jsx: 'automatic',
        drop: ['console', 'debugger'],
    },
});