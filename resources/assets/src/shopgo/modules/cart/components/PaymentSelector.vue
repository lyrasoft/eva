<script setup lang="ts">

import PaymentItem from '~shopgo/modules/cart/components/PaymentItem.vue';
import { AddressFormData, Payment, Shipping } from '~shopgo/types';

defineProps<{
  payments: Payment[];
  paymentData?: AddressFormData | null;
  loading?: boolean;
  shippingId?: string | number;
}>();

const paymentId = defineModel<string | number>();
</script>

<template>
  <div class="l-payments">
    <h3>{{ $lang('shopgo.cart.payment.title') }}</h3>

    <div v-if="payments.length > 0" class="d-flex flex-column gap-3">
      <PaymentItem v-for="(payment, i) of payments" :key="payment.id"
        style="animation-duration: .1s"
        :payment="payment"
        :i="i"
        :selected="String(paymentId) === String(payment.id)"
        v-on:selected="paymentId = payment.id"
      >
      </PaymentItem>
    </div>
    <div v-else class="card bg-light">
      <div class="card-body py-5 text-center">
        <template v-if="loading">
          <span class="spinner spinner-border"></span>
        </template>
        <template v-else-if="shippingId">
          {{ $lang('shopgo.cart.text.no.payments') }}
        </template>
        <template v-else>
          {{ $lang('shopgo.cart.text.select.shipping.first') }}
        </template>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
