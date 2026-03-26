import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    build: {
        outDir: '../../public/build-manufacturing',
        emptyOutDir: true,
        manifest: 'manifest.json',
    },
    plugins: [
        laravel({
            publicDirectory: '../../public',
            buildDirectory: 'build-manufacturing',
            input: [
                __dirname + '/Resources/assets/js/manufacturing-form.js',
            ],
            refresh: true,
        }),
    ],
});
