/**
 * Part of Windwalker Fusion project.
 *
 * @copyright  Copyright (C) 2021 LYRASOFT.
 * @license    MIT
 */

import fusion, { sass, babel, parallel, src, symlink } from '@windwalker-io/fusion';
import { jsSync, installVendors, findModules } from '@windwalker-io/core';

export async function css() {
  // Watch start
  fusion.watch([
    'resources/assets/scss/**/*.scss',
    'src/Module/**/assets/*.scss',
    ...findModules('**/assets/*.scss')
  ]);
  // Watch end

  // Front
  sass(
    [
      'resources/assets/scss/front/main.scss',
      ...findModules('Front/**/assets/*.scss'),
      'src/Module/Front/**/assets/*.scss'
    ],
    'www/assets/css/front/main.css'
  );
  sass(
    'resources/assets/scss/front/bootstrap.scss',
    'www/assets/css/front/bootstrap.css'
  );

  // Admin
  sass(
    [
      'resources/assets/scss/admin/main.scss',
      ...findModules('Admin/**/assets/*.scss'),
      'src/Module/Admin/**/assets/*.scss'
    ],
    'www/assets/css/admin/main.css'
  );
}

export async function js() {
  // Watch start
  fusion.watch(['resources/assets/src/**/*.{js,mjs}']);
  // Watch end

  // Compile Start
  babel('resources/assets/src/**/*.{js,mjs}', 'www/assets/js/', { module: 'systemjs' });
  // Compile end

  return syncJS();
}

export async function images() {
  // Watch start
  fusion.watch('resources/assets/images/**/*');
  // Watch end

  // Compile Start
  return await fusion.copy('resources/assets/images/**/*', 'www/assets/images/')
  // Compile end
}

export async function syncJS() {
  // Watch start
  fusion.watch(['src/Module/**/assets/**/*.{js,mjs}', ...findModules('**/assets/*.{js,mjs}')]);
  // Watch end

  // Compile Start
  const { dest } = await jsSync(
    'src/Module/',
    'www/assets/js/view/'
  );

  babel(dest.path + '**/*.{mjs,js}', null, { module: 'systemjs' });
  // Compile end

  return Promise.all([]);
}

export async function admin() {
  fusion.watch([
    'vendor/lyrasoft/theme-skote/src/**/*',
    'resources/assets/scss/admin/**/*.scss'
  ]);

  sass(
    'theme/admin/src/assets/scss/app.scss',
    'www/assets/css/admin/app.css'
  );
  sass(
    'resources/assets/scss/admin/bootstrap.scss',
    'www/assets/css/admin/bootstrap.css'
  );
  sass(
    'resources/assets/scss/admin/icons.scss',
    'www/assets/css/admin/icons.css'
  );
  babel(
    'theme/admin/src/assets/js/app.js',
    'www/assets/js/admin/app.js'
  );
  src('theme/admin/dist/assets/libs/').pipe(symlink('www/assets/vendor/admin/'));
  src('theme/admin/dist/assets/fonts/').pipe(symlink('www/assets/css/fonts/'));
}

export async function install() {
  installVendors(
    [
      '@fortawesome/fontawesome-pro',
      'wowjs',
      'animate.css',
      'jarallax',
    ],
    [
      'lyrasoft/luna'
    ]
  );

  src('vendor/lyrasoft/theme-skote/').pipe(symlink('theme/admin'));
}

export default parallel(css, js, images);

/*
 * APIs
 *
 * Compile entry:
 * fusion.js(source, dest, options = {})
 * fusion.babel(source, dest, options = {})
 * fusion.module(source, dest, options = {})
 * fusion.ts(source, dest, options = {})
 * fusion.typeScript(source, dest, options = {})
 * fusion.css(source, dest, options = {})
 * fusion.sass(source, dest, options = {})
 * fusion.copy(source, dest, options = {})
 *
 * Live Reload:
 * fusion.livereload(source, dest, options = {})
 * fusion.reload(file)
 *
 * Gulp proxy:
 * fusion.src(source, options)
 * fusion.dest(path, options)
 * fusion.watch(glob, opt, fn)
 * fusion.symlink(directory, options = {})
 * fusion.lastRun(task, precision)
 * fusion.tree(options = {})
 * fusion.series(...tasks)
 * fusion.parallel(...tasks)
 *
 * Stream Helper:
 * fusion.through(handler) // Same as through2.obj()
 *
 * Config:
 * fusion.disableNotification()
 * fusion.enableNotification()
 */
