import { useFusion } from '@windwalker-io/fusion-next';
import { defineConfig } from 'vite';
import { resolve } from 'node:path';

export default defineConfig(({ mode }) => {
  return {
    base: './',
    server: {
      watch: {
        ignored: ['**/.env*']
      }
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
      useFusion(() => import('./fusionfile')),
    ],
  };
});
