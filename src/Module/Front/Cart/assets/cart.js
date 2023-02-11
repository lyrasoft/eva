// JS file for Cart

import '@main';

await u.domready();

const { createApp, ref, toRefs, reactive, computed, watch, provide, nextTick, onMounted } = Vue;

const CartApp = {
  name: 'CartApp',
  props: {

  },
  setup(props) {
    const state = reactive({
      items: [],
      totals: [],
      coupons: [],
      loading: false
    });

    init();

    function init() {

    }

    async function loadItems() {
      const res = await u.$http.get('@cart_ajax/getItems');


    }

    return {

    };
  }
};

const app = createApp(CartApp, u.data('cart.props'));

app.use(ShopGoVuePlugin);
app.mount('cart-app');
