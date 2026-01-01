import vuePlugin from '@vitejs/plugin-vue';
import { useFusion } from '@windwalker-io/fusion-next';
import { defineConfig } from 'vite';
import { resolve } from 'node:path';

export default defineConfig(({ mode }) => {
  return {
    base: './',
    resolve: {
      dedupe: ['@lyrasoft/shopgo', 'vue', '@windwalker-io/unicorn-next'],
    },
    build: {
      sourcemap: false,
      minify: mode === 'production',
    },
    // Fix SASS issues
    css: {
      preprocessorOptions: {
        scss: {
          silenceDeprecations: ['if-function', 'mixed-decls', 'color-functions', 'global-builtin', 'import'],
          loadPaths: [
            resolve('./vendor/lyrasoft/theme-nexus/'),
          ],
        },
      },
    },
    plugins: [
      vuePlugin({
        features: {
          prodDevtools: true,
        },
        template: {
          compilerOptions: {
            preserveWhitespace: false,
            whitespace: 'preserve',
          }
        }
      }),
      useFusion(() => import('./fusionfile')),
    ],
  };
});
