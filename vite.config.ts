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
                  // Default to all origins. This is necessary if using Orbstack.
                  host: '0.0.0.0',
                  cors: {
                      origin: [
                          'http://localhost',
                          'http://localhost:80',
                          'http://127.0.0.1',
                          'http://127.0.0.1:80',
                          'http://laravel.test.react-starter-kit-plus.orb.local',
                          'https://laravel.test.react-starter-kit-plus.orb.local',
                      ],
                      methods: ['GET', 'HEAD'],
                      allowedHeaders: ['Content-Type'],
                      credentials: true,
                  },
              }
            : undefined,
}));
