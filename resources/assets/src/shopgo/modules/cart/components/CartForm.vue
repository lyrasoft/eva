<script setup lang="ts">
import { AddressFormData, Payment, Shipping, User } from '~shopgo/types';

const props = defineProps<{
  user: User | null;
  shippings: Shipping[];
  payments: Payment[];
  checkoutData?: any;
}>();

const paymentData = defineModel<AddressFormData>('payment', {
  required: true,
});

const shippingData = defineModel<AddressFormData>('shipping', {
  required: true,
});

const paymentId = defineModel<any>('payment-id', {
  required: true,
});

const shippingId = defineModel<any>('shipping-id', {
  required: true,
});

const note = defineModel<string>('note', {
  default: '',
});
</script>

<template>
  <div>
    <slot
      :user
      :shippings
      :payments
      :shippingData
      :paymentData
      :shippingId
      :paymentId
      :note
      :checkoutData
    ></slot>
  </div>
</template>

<style scoped>

</style>
