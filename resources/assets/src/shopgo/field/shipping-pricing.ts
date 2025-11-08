

// App
const { ref, onMounted, computed, createApp, toRefs, reactive } = Vue;

const ShippingPricing = {
  name: 'ShippingPricingApp',
  props: {
    pricing: Object
  },
  setup(props) {
    u.$ui.iframeModal();

    const state = reactive({
      global: props.pricing?.global || {
        free: false,
        pricing: getEmptyPricing()
      },
      locationCategories: props.pricing?.locationCategories || [],
      locations: props.pricing?.locations || [],
      currentItem: null,
    });

    const selectModal = ref(null);
    const pricingModal = ref(null);

    function openLocationCategorySelector() {
      const url = u.route('category_modal', { callback: 'locationCategorySelected' });

      window.locationCategorySelected = function ({ value: id, title }) {
        state.locationCategories.push({
          id,
          title,
          free: false,
          pricing: getEmptyPricing(),
        });

        selectModal.value.close();
      }

      selectModal.value.open(url, { size: 'modal-xl' });
    }

    function openLocationSelector() {
      const url = u.route('location_modal', { callback: 'locationSelected' });

      window.locationSelected = function ({ value: id, title, path }) {

        state.locations.push({
          id,
          title,
          path,
          free: false,
          pricing: getEmptyPricing(),
        });

        selectModal.value.close();
      }

      selectModal.value.open(url, { size: 'modal-xl' });
    }

    function configurePricing(item) {
      state.currentItem = item;

      u.$ui.bootstrap.modal(pricingModal.value).show();
    }

    function getEmptyPricing() {
      const seg = getEmptyPricingSegment();
      seg.threshold = 0;

      return [seg];
    }

    function getEmptyPricingSegment() {
      return ShopgoVueUtilities.prepareVueItem(
        {
          threshold: '',
          fee: '',
        }
      );
    }

    function addPricingSegment(i = 0) {
      state.currentItem.pricing.splice(i + 1, 0, getEmptyPricingSegment());
    }

    function removePricingSegment(i) {
      state.currentItem.pricing.splice(i, 1);
    }

    const finalResult = computed(() => {
      return JSON.stringify(
        {
          global: state.global,
          locationCategories: state.locationCategories,
          locations: state.locations,
        }
      );
    });

    return {
      ...toRefs(state),
      finalResult,
      selectModal,
      pricingModal,

      openLocationSelector,
      openLocationCategorySelector,
      configurePricing,
      addPricingSegment,
      removePricingSegment,
    };
  },

  create(el) {
    const id = el.getAttribute('id');

    const app = createApp(
      this,
      u.data(id + '.props')
    );

    app.use(ShopGoVuePlugin);
    app.component('draggable', vuedraggable);
    app.mount(el);
  },
};
