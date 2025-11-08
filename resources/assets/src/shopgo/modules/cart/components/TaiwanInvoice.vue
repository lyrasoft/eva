<script setup lang="ts">
import { inject, Ref, ref } from 'vue';

type InvoiceData = {
  type: 'idv' | 'company';
  carrierCode?: string;
  vat?: string;
  title?: string;
  email?: string;
};

const checkoutData = inject<any>('checkoutData');

const receiptData = ref<InvoiceData>(
  Object.assign(
    {
      type: 'idv',
      carrierCode: '',
      vat: '',
      title: '',
      email: '',
    },
    checkoutData?.receipt || {}
  )
);
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h5 class="l-cart-page__title mb-4">發票選項</h5>

      <div class="form-check mb-0">
        <input type="radio" class="form-check-input" id="input-receipt-type"
          name="checkout[receipt][type]"
          checked
          v-model="receiptData.type"
          value="idv"
        />
        <label for="input-receipt-type">二聯式發票（個人）</label>
      </div>

      <div class="mt-3">
        <label for="input-receipt-carrierCode" class="form-label">
          發票載具編號
        </label>
        <input id="input-receipt-carrierCode" type="text"
          class="form-control"
          placeholder="/XXXXXXX"
          name="checkout[receipt][carrierCode]"
          v-model="receiptData.carrierCode"
          autocomplete="invoice"
          :disabled="receiptData.type !== 'idv'"
          oninput="this.value = this.value.toUpperCase()"
          pattern="\/[A-Z0-9+-\.]{7}"
        />
      </div>

      <hr class="my-4" />

      <div class="form-check mb-3">
        <input type="radio" class="form-check-input" id="input-receipt-type2"
          name="checkout[receipt][type]"
          v-model="receiptData.type"
          value="company"
        />
        <label for="input-receipt-type2">三聯式發票含統編（公司用）</label>
      </div>

      <div class="d-md-flex gap-3 mb-3">
        <input type="text" class="form-control flex-grow-1 mb-3 mb-md-0"
          placeholder="輸入統編"
          name="checkout[receipt][vat]"
          v-model="receiptData.vat"
          :disabled="receiptData.type !== 'company'"
          required
          autocomplete="vat"
        />
        <input type="text" class="form-control flex-grow-1" placeholder="輸入抬頭"
          name="checkout[receipt][title]"
          v-model="receiptData.title"
          :disabled="receiptData.type !== 'company'"
          required
          autocomplete="organization"
        />
      </div>

      <div>
        <input type="email" class="form-control" placeholder="輸入聯絡信箱"
          name="checkout[receipt][email]"
          v-model="receiptData.email"
          :disabled="receiptData.type !== 'company'"
          required
          autocomplete="email"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
