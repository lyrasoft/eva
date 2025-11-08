
import { initApp } from '~/src/shopgo/modules/cart/cart';
import { data, domready } from '@windwalker-io/unicorn-next';

await domready();

const app = await initApp(data('cart.props'));
app.mount('cart-app');
