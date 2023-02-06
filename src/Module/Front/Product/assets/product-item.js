// JS file for ProductItem

import '@main';

await u.domready();

const { createApp, ref, toRefs, reactive, computed, watch, provide, nextTick } = Vue;

const ProductItemApp = {
  name: 'ProductItemApp',
  props: {
    product: Object,
    features: Object,
    mainVariant: Object,
  },
  setup(props) {
    const state = reactive({
      selected: {},
      currentVariant: null
    });

    if (props.product.variants === 0) {
      state.currentVariant = props.mainVariant;
    }

    const allSelected = computed(() => {
      return Object.values(props.features).length === Object.values(state.selected).length;
    });

    watch(() => state.selected, () => {
      if (allSelected.value) {
        findVariant();
      }
    }, { deep: true });

    async function findVariant() {
      const options = Object.values(state.selected).map(option => option.uid);

      const res = await u.$http.get(
        '@product_ajax/getVariant',
        {
          params: {
            product_id: props.product.id,
            options
          }
        }
      );

      console.log(res.data);
    }

    function toggleOption(option, feature) {
      state.selected[feature.id] = option;
    }

    function isSelected(option, feature) {
      return state.selected[feature.id]?.uid === option.uid;
    }

    return {
      ...toRefs(state),
      allSelected,

      toggleOption,
      isSelected,
    };
  }
};

const app = createApp(ProductItemApp, u.data('product.item.props'));

app.use(ShopGoVuePlugin);
app.mount('#product-item-app');
