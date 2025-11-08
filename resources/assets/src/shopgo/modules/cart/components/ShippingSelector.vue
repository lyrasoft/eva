<script setup lang="ts">
import ShippingItem from '~shopgo/modules/cart/components/ShippingItem.vue';
import { AddressFormData, Shipping } from '~shopgo/types';

defineProps<{
  shippings: Shipping[];
  shippingData?: AddressFormData | null;
  loading?: boolean;
}>();

const shippingId = defineModel<string | number>();
</script>

<template>
  <div class="l-shippings">
    <h3>{{ $lang('shopgo.cart.shipping.title') }}</h3>

    <div v-if="shippings.length > 0" class="d-flex flex-column gap-3">
      <ShippingItem v-for="(shipping, i) of shippings" :key="shipping.id"
        style="animation-duration: .1s"
        :shipping="shipping"
        :i="i"
        :selected="String(shippingId) === String(shipping.id)"
        v-on:selected="shippingId = shipping.id"
      >
      </ShippingItem>
    </div>
    <div v-else class="card bg-light">
      <div class="card-body py-5 text-center">
        <template v-if="loading">
          <span class="spinner spinner-border"></span>
        </template>
        <template v-else-if="shippingData?.locationId">
          {{ $lang('shopgo.cart.text.no.shippings') }}
        </template>
        <template v-else>
          {{ $lang('shopgo.cart.text.select.location.first') }}
        </template>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
