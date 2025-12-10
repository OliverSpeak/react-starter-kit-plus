import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig(({ command }) => ({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
    esbuild: {
        jsx: 'automatic',
    },
    server:
        command === 'serve'
            ? {
                  host: '0.0.0.0', // Bind to all interfaces (for Docker)
                  hmr: {
                    // Specify the hostname. This is necessary for platforms that use domain names per container, such as Orbstack.
                      host: 'laravel.test.react-starter-kit-plus.orb.local',
                  },
              }
            : undefined,
}));
