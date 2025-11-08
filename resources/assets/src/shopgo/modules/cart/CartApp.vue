<script setup lang="ts">
import { __, data, debounce, route, simpleAlert, useHttpClient, useQueue, useStack } from '@windwalker-io/unicorn-next';
import { computed, getCurrentInstance, inject, nextTick, onMounted, provide, ref, watch } from 'vue';
import { ComponentExposed } from 'vue-component-type-helpers';
import TaiwanInvoice from '~js/shopgo/modules/cart/components/TaiwanInvoice.vue';
import AddressForm from '~shopgo/modules/cart/components/AddressForm.vue';
import AddressFormSet from '~shopgo/modules/cart/components/AddressFormSet.vue';
import CartForm from '~shopgo/modules/cart/components/CartForm.vue';
import CartListItem from '~shopgo/modules/cart/components/CartListItem.vue';
import CartSidebar from '~shopgo/modules/cart/components/CartSidebar.vue';
import PaymentSelector from '~shopgo/modules/cart/components/PaymentSelector.vue';
import ShippingSelector from '~shopgo/modules/cart/components/ShippingSelector.vue';
import { CartData, CartItem, Discount, OrderTotal, Payment, Shipping, User } from '~shopgo/types';

const props = defineProps<{
  user: User | null;
  checkoutData: any;
}>();

const loaded = ref(false);
const items = ref<CartItem[]>([]);
const totals = ref<Record<string, OrderTotal>>({});
const coupons = ref<Discount[]>([]);
const paymentId = ref(props.checkoutData?.payment?.id || '');
const paymentData = ref(props.checkoutData?.payment_data || {});
const shippingId = ref(props.checkoutData?.shipping?.id || '');
const shippingData = ref(props.checkoutData?.shipping_data || {});
const shippings = ref<Shipping[]>([]);
const payments = ref<Payment[]>([]);
const code = ref('');
const note = ref(props.checkoutData?.note || '');
const loading = ref(false);
const partialCheckout = ref(data('partial.checkout'));
const queue = useQueue('shopgo.cart');
const loadingStack = useStack('loading');

provide('checkoutData', props.checkoutData);
provide('items', items);
provide('totals', totals);
provide('coupons', coupons);
provide('shippingData', shippingData);
provide('paymentData', paymentData);
provide('shippingId', shippingId);
provide('paymentId', paymentId);
provide('shippings', shippings);
provide('payments', payments);
provide('loading', loading);
provide('code', code);
provide('note', note);
provide('queue', queue);
provide('partialCheckout', partialCheckout);

let abort: AbortController | null = null;
const form = document.querySelector<HTMLFormElement>('#cart-form')!;
const toggleAllInput = ref<HTMLInputElement>();

// Forms
const inc = getCurrentInstance();
const components = inc?.appContext.components || {};

function resolveOverrideComponent(name: string) {
  return components[name] ?? null;
}
const AfterAddressForm = resolveOverrideComponent('AfterAddressForm');
const AfterShippingForm = resolveOverrideComponent('AfterShippingForm');
const AfterPaymentForm = resolveOverrideComponent('AfterPaymentForm');
const AfterNoteForm = resolveOverrideComponent('AfterNoteForm');

loadingStack.observe((stack, length) => {
  loading.value = length > 0;
});

init();

function popLoading(wait = 300) {
  setTimeout(() => {
    loadingStack.pop();
  }, wait);
}

const afterItemsChanged = debounce(function () {
  return loadItems();
}, 300);

async function loadItems(updateShippings = true) {
  abort?.abort('Cancel by next load');
  abort = new AbortController();

  loadingStack.push(true);

  const { get, isAxiosError, isCancel } = await useHttpClient();

  try {
    const res = await get(
      '@cart_ajax/getItems',
      {
        params: {
          location_id: shippingData.value.locationId,
          shipping_id: shippingId.value,
          payment_id: paymentId.value,
        },
        signal: abort.signal
      }
    );

    await setCartData(res.data.data, updateShippings);

    return res;
  } catch (e) {
    if (isCancel(e)) {
      console.log(e.message);
    }

    console.error(e);
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
  } finally {
    popLoading();

    abort = null;
  }
}

async function setCartData(data: CartData, updateShippings = true) {
  items.value = data.items;
  totals.value = data.totals;
  coupons.value = data.coupons;

  if (updateShippings) {
    return await loadShippings();
  }

  return;
}

// Toggle checks
watch(items, () => {
  updateToggleAll();
}, { deep: true });

const itemChecks = computed(() => {
  return items.value.map((item: any) => {
    if (item.options.checked == null) {
      return true;
    }

    return item.options.checked;
  });
});

const checks = computed(() => itemChecks.value.filter(checked => checked === true).length);
const unchecks = computed(() => itemChecks.value.filter(checked => checked === false).length);

function updateToggleAll() {
  if (!toggleAllInput.value) {
    return;
  }

  toggleAllInput.value.checked = false;
  toggleAllInput.value.indeterminate = false;

  if (checks.value > 0 && unchecks.value === 0) {
    toggleAllInput.value.checked = true;
  } else if (unchecks.value > 0 && checks.value === 0) {
    toggleAllInput.value.checked = false;
  } else if (checks.value > 0 && unchecks.value > 0) {
    toggleAllInput.value.indeterminate = true;
  }
}

function toggleChecked() {
  if (!toggleAllInput.value) {
    return;
  }

  for (const item of items.value) {
    item.options.checked = toggleAllInput.value.checked;
  }

  updateChecks();
}

const updateChecks = debounce(async () => {
  abort?.abort('Cancel by next modify.');
  abort = new AbortController();

  const checks: Record<string, '1' | '0'> = {};

  for (const item of items.value) {
    checks[item.key] = item.options.checked ? '1' : '0';
  }

  loadingStack.push(true);

  const { post, isAxiosError } = await useHttpClient();

  try {
    const res = await post('@cart_ajax/updateChecks', { checks }, { signal: abort.signal });

    return await loadItems();
  } catch (e) {
    console.error(e);
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
  } finally {
    popLoading();
    abort = null;
  }
}, 300);

onMounted(() => {
  calcNavAndStickySidebar(form);
});

function calcNavAndStickySidebar(form: HTMLFormElement, offsets = 30) {
  const navbar = document.querySelector<HTMLDivElement>('header .navbar, .navbar');

  if (!navbar) {
    return;
  }

  const top = navbar.clientHeight + offsets;

  form.style.setProperty('--sidebar-offsets-top', top + 'px');
}

async function init() {
  await loadItems();

  loaded.value = true;
}

// Actions
async function removeItem(item: CartItem, i: number) {
  loadingStack.push(true);

  const { delete: del, isAxiosError } = await useHttpClient();

  try {
    const res = await del(`@cart_ajax/removeItem?key=${item.key}`);

    return await afterItemsChanged();
  } catch (e) {
    console.error(e);
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
  } finally {
    setTimeout(() => {
      loadingStack.pop();
    }, 300);
  }
}

async function clearCart() {
  loadingStack.push(true);

  const { put, isAxiosError } = await useHttpClient();

  try {
    await put(`@cart_ajax/clearCart`);

    await loadItems();

    await simpleAlert(
      __('shopgo.cart.message.items.removed'),
      __('shopgo.cart.message.will.back.to.home'),
      'success'
    );

    location.href = route('home');
  } catch (e) {
    console.error(e);
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
  } finally {
    loadingStack.pop();
  }
}

// Quantity
async function changeItemQuantity(item: CartItem, offsets: number) {
  item.quantity += offsets;

  item.quantity = Math.max(item.quantity, 1);

  await updateQuantities(item);
}

const updateQuantities = debounce(async (item: CartItem) => {
  item.quantity = Math.max(item.quantity, 1);

  const values: Record<string, number> = {};

  for (const item of items.value) {
    values[item.key] = item.quantity;
  }

  loadingStack.push(true);

  const { post, isAxiosError } = await useHttpClient();

  try {
    const res = await queue.push(() => post('@cart_ajax/updateQuantities', { values }));

    return await loadItems();
  } catch (e) {
    console.error(e);
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
  } finally {
    popLoading();
  }
}, 600);

// Code / Coupons
async function addCode() {
  if (code.value === '') {
    return;
  }

  loadingStack.push(true);

  const { post, isAxiosError } = await useHttpClient();

  try {
    const res = await post('@cart_ajax/addCode', { code: code.value });

    code.value = '';

    await loadItems();
  } catch (e) {
    console.error(e);
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
  } finally {
    popLoading();
  }
}

async function removeCode(id: number | string) {
  loadingStack.push(true);

  const { delete: del, isAxiosError } = await useHttpClient();

  try {
    const res = await del('@cart_ajax/removeCode', { id });

    await loadItems();
  } catch (e) {
    console.error(e);
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
  } finally {
    popLoading();
  }
}

// Shippings
watch(() => shippingData.value.locationId, () => {
  loadShippings();
});
watch(() => shippingId.value, () => {
  loadItems(false);
});

const selectedShipping = computed(() => {
  return shippings.value.find((item: any) => String(item.id) === String(shippingId.value));
});

const loadShippings = debounce(async function () {
  loadingStack.push(true);

  const { get, isAxiosError } = await useHttpClient();

  try {
    const res = await get(`@cart_ajax/shippings?location_id=${shippingData.value.locationId}`);

    shippings.value = res.data.data;

    await nextTick();
    await nextTick();

    if (shippings.value.length > 0) {
      if (!selectedShipping.value) {
        shippingId.value = shippings.value[0].id;
      }
    } else {
      shippingId.value = null;
    }
  } catch (e) {
    console.error(e);
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
  } finally {
    popLoading();
  }
}, 300);

// Payments
watch(() => [shippingData.value.locationId, shippingId.value], () => {
  loadPayments();
});

const selectedPayment = computed(() => {
  return payments.value.find((item: any) => item.id === paymentId.value);
});

const loadPayments = debounce(async function () {
  loadingStack.push(true);

  const { get, isAxiosError } = await useHttpClient();

  try {
    const res = await get(
      `@cart_ajax/payments`,
      {
        params: {
          location_id: shippingData.value.locationId,
          shipping_id: shippingId.value
        }
      }
    );

    payments.value = res.data.data;

    await nextTick();
    await nextTick();

    if (payments.value.length > 0) {
      if (!payments.value.find((payment: any) => payment.id === paymentId.value)) {
        paymentId.value = payments.value[0].id;
      }
    } else {
      paymentId.value = null;
    }
  } catch (e) {
    console.error(e);
    if (isAxiosError(e)) {
      simpleAlert(e.message, '', 'warning');
    }
  } finally {
    popLoading();
  }
}, 300);

// Checkout
const canCheckout = computed(() => {
  if (checks.value === 0) {
    return false;
  }

  if (!shippingData.value.locationId) {
    return false;
  }

  if (!paymentData.value.locationId) {
    return false;
  }

  if (!shippingId.value) {
    return false;
  }

  if (!paymentId.value) {
    return false;
  }

  return true;
});

const shippingForm = ref<ComponentExposed<typeof AddressForm>>();
const paymentForm = ref<ComponentExposed<typeof AddressForm>>();

function checkout() {
  if (checks.value === 0) {
    console.warn('No checked items');
    return;
  }

  if (Number(totals.value.grand_total.price) < 0) {
    swal('Cannot process cart with negative prices.', '', 'warning');
    return;
  }

  for (const item of items.value) {
    if (Number(item.priceSet.final_total.price) < 0) {
      swal('Cannot process product items with negative prices.', '', 'warning');
      return;
    }

    if (Number(item.priceSet.attached_final_total.price) < 0) {
      swal('Cannot process product items with negative prices.', '', 'warning');
      return;
    }
  }

  if (shippingForm.value && !shippingForm.value.validate()) {
    console.log('Shipping Validate Fail');
    return;
  }

  if (paymentForm.value && !paymentForm.value.validate()) {
    console.log('Payment Validate Fail');
    return;
  }

  if (!form.checkValidity()) {
    form.reportValidity();

    const invalid = form.querySelector<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>(':invalid');

    if (invalid && !isVisible(invalid) && invalid.dataset.validationMessage) {
      simpleAlert(invalid.dataset.validationMessage);
    }

    return;
  }

  loading.value = true;

  form.requestSubmit();
}

function isVisible(el: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement) {
  return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
}
</script>

<template>

  <div class="row">
    <div class="col-lg-8 l-cart-page__content">
      <!-- Header -->
      <header class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-2">
          <h3 class="m-0">{{ $lang('shopgo.cart.title') }}</h3>
          <div v-if="partialCheckout" class="form-check">
            <input id="input-toggle-all" type="checkbox" class="form-check-input"
              ref="toggleAllInput"
              @click="toggleChecked"
            />
            <label for="input-toggle-all" class="form-check-label">
              {{ $lang('shopgo.cart.toggle.all') }}
            </label>
          </div>
          <div v-if="loading" class="spinner spinner-border-sm spinner-border"
            data-cloak>

          </div>
        </div>

        <div>
          <a href="javascript://"
            @click="clearCart">
            <i class="fa fa-times"></i>
            {{ $lang('shopgo.cart.button.remove.all') }}
          </a>
        </div>
      </header>

      <div class="l-cart-data d-flex flex-column gap-4">

        <!-- Cart Items -->
        <div class="l-cart-items">
          <CartListItem v-for="(item, i) of items" :key="item.key"
            :item
            :has-checkbox="partialCheckout"
            @remove-item="removeItem(item, i)"
            @update-quantity="updateQuantities(item)"
            @change-item-quantity="changeItemQuantity(item, $event)"
            @update-checks="updateChecks"
          />
        </div>

        <CartForm class="l-cart-form d-flex flex-column gap-4"
          :user
          :shippings
          :payments
          :checkoutData
          v-model:payment="paymentData"
          v-model:shipping="shippingData"
          v-model:shipping-id="shippingId"
          v-model:payment-id="paymentId"
        >
          <!-- Addresses -->
          <div class="l-cart-address-set">
            <AddressFormSet :user v-model:payment="paymentData" v-model:shipping="shippingData" />
          </div>

          <Component v-if="AfterAddressForm" :is="AfterAddressForm" />

          <!-- Shippings -->
          <ShippingSelector :shippings :shippingData v-model="shippingId" />

          <Component v-if="AfterShippingForm" :is="AfterShippingForm" />

          <!-- Payments -->
          <PaymentSelector :payments :paymentData :shippingId v-model="paymentId" />

          <TaiwanInvoice />

          <Component v-if="AfterPaymentForm" :is="AfterPaymentForm" />

          <!-- Note -->
          <div class="l-checkout-note card mb-4">
            <div class="card-body">
              <h5 class="card-title mb-3">
                {{ $lang('shopgo.cart.field.note') }}
              </h5>

              <textarea rows="4"
                class="form-control"
                v-model="note"
                name="checkout[note]"
                :placeholder="$lang('shopgo.cart.field.note.placeholder')"
              ></textarea>
            </div>
          </div>

          <Component v-if="AfterNoteForm" :is="AfterNoteForm" />
        </CartForm>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4 l-cart-page__sidebar">
      <CartSidebar
        :totals
        :coupons
        :loaded
        :loading
        :selectedShipping
        :selectedPayment
        :canCheckout
        v-model:code="code"
        @add-code="addCode"
        @remove-code="removeCode"
        @checkout="checkout"
      />
    </div>
  </div>
</template>

<style scoped>

</style>
