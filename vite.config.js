import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: '/assets/', // OR './' if shared hosting
    plugins: [vue()],
    
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
