import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig(({ mode }) => ({
  plugins: [vue()],

  build: {
    outDir: './',
    emptyOutDir: false,  // CRITICAL: prevent wiping plugin root
    rollupOptions: {
      input: {
        'assets/js/bring-fraktguiden-settings': resolve(__dirname, 'resources/js/bring-fraktguiden-settings.js'),
        'assets/js/bring-fraktguiden-checkout': resolve(__dirname, 'resources/js/bring-fraktguiden-checkout.js'),
        'pro/assets/js/booking': resolve(__dirname, 'pro/resources/js/booking.js'),
      },
      output: {
        entryFileNames: '[name].js',
        chunkFileNames: 'assets/js/shared/[name].js',  // Predictable chunk names (no hash)
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'style.css') {
            return 'assets/css/compiled-styles.css';
          }
          return '[name].[ext]';
        },
        manualChunks: {
          'vue-runtime': ['vue'],  // Explicitly chunk Vue
        },
      },
      external: ['jquery'],
      preserveEntrySignatures: 'exports-only',
    },

    cssCodeSplit: false,  // Inline CSS in JS bundles
    minify: mode === 'production',  // Minify only in production
    target: 'es2015',
  },

  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources/js'),
      '@pro': resolve(__dirname, 'pro/resources/js'),
    },
  },

  css: {
    preprocessorOptions: { scss: {} }
  },
}));
