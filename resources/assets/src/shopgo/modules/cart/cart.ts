import { useCssImport } from '@windwalker-io/unicorn-next';
import { createApp } from 'vue';
import CartApp from '~shopgo/modules/cart/CartApp.vue';
import { ShopGoPlugin } from '~shopgo/shopgo-plugin';

export function initApp(props: Record<string, any>) {
  useCssImport('@vue-animate');

  const app = createApp(CartApp, props);

  app.use(ShopGoPlugin);

  return app;
}


