import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => ({
  plugins: [vue()],
  build: {
    outDir: 'build',
    rollupOptions: {
      input: [
        'resources/js/admin.js',
        'resources/js/booking-box.js',
        'resources/js/checkout.js',
        'resources/js/home.js',
        'resources/js/pro.js',
      ],
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/shared/[name].js',
        assetFileNames: 'css/[name].[ext]',
        manualChunks: { 'vue-runtime': ['vue'] },
      },
      external: ['jquery'],
    },
  }
}));
