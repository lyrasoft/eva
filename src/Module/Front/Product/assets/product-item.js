// JS file for ProductItem

import '@main';

await u.domready();

const { createApp, ref, toRefs, reactive, computed, watch, provide, nextTick, onMounted } = Vue;

const ProductItemApp = {
  name: 'ProductItemApp',
  props: {
    product: Object,
    features: Object,
    mainVariant: Object,
    discounts: Array,
  },
  setup(props) {
    const state = reactive({
      imageView: u.data('image.default'),
      selected: {},
      currentVariant: null,
      hasSubVariants: props.product.variants !== 0,
      quantity: 1
    });

    if (!state.hasSubVariants) {
      state.currentVariant = props.mainVariant;
    }

    const hasDiscount = computed(() => {
      return Number(state.currentVariant.priceSet.base.price) !== Number(state?.currentVariant?.priceSet?.final?.price);
    });

    // Stock
    const outOfStock = computed(() => {
      if (!state?.currentVariant?.subtract) {
        return false;
      }

      return Number(state.currentVariant.stockQuantity) - props.product.safeStock < state.quantity;
    });

    // Quantity
    watch(() => state.quantity, (qty) => {
      if (qty < 1) {
        state.quantity = 1;
      }
    });

    // Images
    const swiper = ref(null);

    onMounted(() => {
      new Swiper(swiper.value, {
        simulateTouch: true,
        allowTouchMove: true,
        autoHeight: true,
        slidesPerView: 6,
        spaceBetween: 8,
        observe: true,
        rewind: true,
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
      });
    });

    const images = computed(() => {
      let imgs = [];
      let imgView = null;

      if (state.currentVariant) {
        if (!state.currentVariant.primary) {
          imgs = [...props.mainVariant.images, ...state.currentVariant.images];
        } else {
          imgs = state.currentVariant.images;
        }

        imgView = state.currentVariant.images[0];
      } else {
        imgs = props.mainVariant.images;
        imgView = props.mainVariant.images[0];
      }

      state.imageView = imgView?.url || u.data('image.default');

      return imgs;
    });

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

      const { variant, discounts } = res.data.data;

      state.currentVariant = variant;
      state.discounts = discounts;
    }

    const errorMsg = 'shopgo.product.message.variant.not.found';

    function toggleOption(option, feature) {
      state.selected[feature.id] = option;
    }

    function isSelected(option, feature) {
      return state.selected[feature.id]?.uid === option.uid;
    }

    // Discounts
    const discountNotices = computed(() => {
      if (!state.currentVariant) {
        return [];
      }

      const items = [];

      for (const discount of props.discounts) {
        let price = null;

        if (discount.method === 'fixed') {
          price = discount.price;
        } else if (discount.method === 'offsets') {
          price = state.currentVariant.price + discount.price;
        } else {
          price = state.currentVariant.price * discount.price / 100;
        }

        const item = {
          minProductQuantity: discount.minProductQuantity,
          price
        };

        items.push(item);
      }

      return items;
    });

    return {
      ...toRefs(state),
      allSelected,
      hasDiscount,
      outOfStock,
      swiper,
      images,
      discountNotices,

      toggleOption,
      isSelected,
    };
  }
};

const app = createApp(ProductItemApp, u.data('product.item.props'));

app.use(ShopGoVuePlugin);
app.mount('#product-item-app');
