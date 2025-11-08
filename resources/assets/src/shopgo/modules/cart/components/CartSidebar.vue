<script setup lang="ts">

import { computed } from 'vue';
import { vTooltip } from '~shopgo/directives';
import { Discount, OrderTotal, Payment, Shipping } from '~shopgo/types';

const props = defineProps<{
  totals: Record<string, OrderTotal>;
  coupons: Discount[];
  loading?: boolean;
  loaded?: boolean;
  selectedShipping?: Shipping | null;
  selectedPayment?: Payment | null;
  canCheckout?: boolean;
}>();

const emits = defineEmits<{
  'add-code': [code: string];
  'remove-code': [id: number | string];
  'checkout': [];
}>()

// Code / Coupons
const code = defineModel<string>('code', {
  default: ''
});

async function addCode() {
  if (code.value === '') {
    return;
  }

  emits('add-code', code.value);
}

async function removeCode(id: number | string) {
  emits('remove-code', id);
}

// totals
const filteredTotals = computed(() => {
  const _totals: OrderTotal[] = [];

  for (const name in props.totals) {
    if (name === 'total') {
      continue;
    }

    if (name === 'grand_total') {
      continue;
    }

    const total = props.totals[name];

    if (Number(total.price) === 0) {
      continue;
    }

    _totals.push(total);
  }

  return _totals;
});

// Checkout
function checkout() {
  emits('checkout');
}
</script>

<template>
  <div class="l-cart-sidebar position-sticky"
    style="top: var(--sidebar-offsets-top, 90px);"
  >
    <div class="card">
      <!-- Code Input -->
      <div class="card-body l-cart-coupons border-bottom">
        <h5>{{ $lang('shopgo.cart.label.discount.code') }}</h5>
        <div class="d-flex gap-2">
          <input type="text" class="form-control" v-model="code" />
          <button type="button" class="btn btn-secondary text-nowrap"
            style="min-width: 100px"
            @click="addCode"
            :disabled="code === '' || loading"
          >
            {{ $lang('shopgo.cart.button.use.discount.code') }}
          </button>
        </div>

        <!-- Coupons -->
        <div v-if="coupons.length" data-cloak class="list-group list-group-flush mt-4">
          <div v-for="coupon of coupons" class="list-group-item border-top d-flex">
            <div>
              <div>
                <strong>
                  {{ coupon.title }}
                </strong>
              </div>
              <div class="small text-muted">
                {{ coupon.code }}
              </div>
            </div>

            <div class="ms-auto">
              <a href="javascript://"
                class="link-secondary"
                v-tooltip
                title="{{ $lang('shopgo.cart.button.remove.discount.code') }}"
                @click="removeCode(coupon.id)">
                <i class="fa fa-trash"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Totals Loading -->
      <div v-if="!loaded" class="card-body">
        <div class="card-text placeholder-glow d-flex my-2">
          <span class="placeholder col-4"></span>
          <span class="placeholder col-3 ms-auto"></span>
        </div>
      </div>

      <!-- Totals -->
      <div v-if="loaded" data-cloak class="card-body l-cart-totals text-end">
        <div class="l-cart-total d-flex justify-content-between gap-1 mb-1 w-100">
          <div class="l-cart-total__label">
            {{ $lang('shopgo.cart.label.total') }}
          </div>

          <div v-if="totals.total" class="l-cart-total__value">
            {{ $formatPrice(totals.total.price, { code: true }) }}
          </div>
        </div>

        <div class="l-cart-total d-flex justify-content-between gap-1 mb-1 w-100"
          v-for="total of filteredTotals">
          <div class="l-cart-total__label d-flex gap-2">
            <div>
              {{ total.label }}
            </div>
            <div
              v-if="total.params.type === 'coupon' || total.params.subtype === 'code'">
              <small>({{ total.params.code }})</small>
            </div>
          </div>

          <div class="l-cart-total__value">
            {{ $formatPrice(total.price, { code: true }) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Checkbox -->
    <div class="card mt-3 position-sticky"
      style="bottom: 0;">
      <div class="card-body d-grid gap-3">
        <!-- Grand Total -->
        <div v-if="loaded"
          class="l-cart-total d-flex justify-content-between gap-1 w-100 fs-5 fw-bold"
          data-cloak>
          <div class="l-cart-total__label">
            {{ $lang('shopgo.cart.label.grand.total') }}
          </div>

          <div v-if="totals.grand_total" class="l-cart-total__value text-end">
            <div>
              {{ $formatPrice(totals.grand_total.price, { code: true }) }}
            </div>
            <div v-if="$currency.isSubCurrency()" class="mt-1 small text-muted fw-normal">
              ({{ $currency.formatMainCurrency(totals.grand_total.price, { code: true }) }})
            </div>
          </div>
        </div>

        <!-- Shipping / Payment Info -->
        <div v-if="loaded" class="d-flex justify-content-between"
          data-cloak>
          <div>
            <i class="fa fa-truck"></i>
            {{ selectedShipping?.title || $lang('shopgo.message.no.shipping.selected') }}
          </div>

          <div>
            <i class="fa fa-credit-card"></i>
            {{ selectedPayment?.title || $lang('shopgo.message.no.payment.selected') }}
          </div>
        </div>

        <!-- Loading -->
        <div v-if="!loaded">
          <div class="card-text placeholder-glow d-flex mb-1" style="height: 1.25rem;">
            <span class="placeholder col-3"></span>
            <span class="placeholder col-4 ms-auto"></span>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="!loaded">
          <div class="card-text placeholder-glow d-flex">
            <span class="placeholder col-3"></span>
            <span class="placeholder col-3 ms-auto"></span>
          </div>
        </div>

        <!-- Checkout Button -->
        <button type="button" class="btn btn-primary btn-lg"
          :disabled="loading || !canCheckout"
          @click="checkout"
        >
          <template v-if="loading">
            <span class="spinner spinner-grow spinner-grow-sm"></span>
          </template>
          <template v-else>
            {{ $lang('shopgo.cart.button.process.checkout') }}
          </template>
        </button>
      </div>
    </div>
  </div>
  <!-- End Sidebar-->
</template>

<style scoped>

</style>
