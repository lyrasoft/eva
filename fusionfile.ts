import fusion, { callbackAfterBuild, symlink } from '@windwalker-io/fusion-next';
import {
  cloneAssets,
  cssModulize,
  findModules,
  installVendors,
  globalAssets,
  jsModulize
} from '@windwalker-io/core/next';
import { resolve } from 'node:path';

// Our Dir
fusion.outDir('www/assets/');

// Aliases
fusion.alias('~', resolve('./resources/assets/src'));
fusion.alias('~vendor', resolve('./www/assets/vendor'));
fusion.alias('~theme', resolve('./theme'));
fusion.alias('vue', 'vue/dist/vue.esm-bundler.js');

// Fusion Options
fusion.overrideOptions({
  chunkNameObfuscation: true
});

// Watch all blade files for changes
// fusion.fullReloads(
//   './src/Module/**/*.blade.php'
// );

// Assets
fusion.plugin(globalAssets({
  clone: {
    //
  },
  reposition: {
    //
  }
}));

export function css() {
  fusion.clean('*.css', '*.css.map', 'css/**/*');

  return [
    // Front
    cssModulize('resources/assets/scss/front/main.scss', 'css/front/main.css')
      .mergeCss(
        findModules('Front/**/assets/*.scss'),
      )
      .parseBlades(
        findModules('Front/**/*.blade.php'),
        findModules('views/**/*.blade.php'),
      ),
    // Admin
    cssModulize('resources/assets/scss/admin/main.scss', 'css/admin/main.css')
      .mergeCss(
        findModules('Admin/**/assets/*.scss'),
      )
      .parseBlades(
        findModules('Admin/**/*.blade.php'),
        findModules('views/**/*.blade.php'),
      ),
    //Nexus
    fusion.css('resources/assets/scss/admin/nexus.scss', 'css/admin/nexus.css'),
  ];
}

export function js() {
  fusion.clean('*.js', 'js/**/*', 'chunks/**/*', 'vite/**/*');

  return [
    jsModulize('resources/assets/src/front/main.ts', 'js/front/main.js')
      .stage('front')
      .mergeScripts(
        findModules('Front/**/assets/*.ts'),
      )
      .parseBlades(
        findModules('Front/**/*.blade.php'),
        findModules('views/**/*.blade.php'),
      ),
    jsModulize('resources/assets/src/admin/main.ts', 'js/admin/main.js')
      .stage('admin')
      .mergeScripts(
        findModules('Admin/**/assets/*.ts'),
      )
      .parseBlades(
        findModules('Admin/**/*.blade.php'),
        findModules('views/**/*.blade.php'),
      ),
  ];
}

export function images() {
  return [
    cloneAssets({
      'resources/assets/images/**/*': 'images/',
    })
  ];
}

export function install() {
  return [
    installVendors(
      [
        '@fortawesome/fontawesome-pro',
        'wowjs',
        'animate.css',
        'jarallax',
        'swiper',
        'youtube-background',
      ],
    ),
    callbackAfterBuild(async () => {
      await symlink('vendor/lyrasoft/theme-nexus/', 'theme/nexus/');
      await symlink('theme/nexus/src/js/', 'www/assets/vendor/nexus/');
    })
  ];
}

export default [js, css, images];
