<script setup lang="ts">
import AddressForm from '~shopgo/modules/cart/components/AddressForm.vue';
import { AddressFormData, User } from '~shopgo/types';

defineProps<{
  user: User | null;
}>();

const paymentData = defineModel<AddressFormData>('payment', {
  required: true,
});

const shippingData = defineModel<AddressFormData>('shipping', {
  required: true,
});

</script>

<template>
  <div class="">
    <AddressForm type="payment"
      :title="$lang('shopgo.cart.payment.data.title')"
      :user="user"
      v-model="paymentData"
      ref="paymentForm"
    ></AddressForm>
    <AddressForm type="shipping"
      :title="$lang('shopgo.cart.shipping.data.title')"
      :user="user"
      v-model="shippingData"
      :sync-data="paymentData"
      ref="shippingForm"
    ></AddressForm>
  </div>
</template>

<style scoped>

</style>
