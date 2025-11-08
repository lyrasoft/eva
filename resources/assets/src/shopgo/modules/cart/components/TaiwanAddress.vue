<script setup lang="ts">

import { onMounted, useTemplateRef } from 'vue';
import TwCitySelector from 'tw-city-selector';
import { AddressFormData } from '~shopgo/types';

const props = defineProps<{
  type: 'shipping' | 'payment';
}>()

const modelValue = defineModel<AddressFormData>({
  required: true,
});
const form = useTemplateRef<HTMLDivElement>('form');

modelValue.value.locationId = 212;

onMounted(() => {
  new TwCitySelector({
    el: '[data-role="address-form"]',
    elCounty: '[data-city]',
    elDistrict: '[data-dist]',
    elZipcode: '[data-zip]',
    countyFieldName: buildInputName('state'),
    districtFieldName: buildInputName('city'),
    zipcodeFieldName: buildInputName('postcode'),
  });
});

function buildInputId(name: string) {
  return `input-${props.type}-${name}`;
}

function buildInputName(name: string) {
  return `checkout[${props.type}_data][${name}]`;
}

</script>

<template>
<div ref="form" data-role="address-form">
  <div class="row">
    <div class="col-lg-6">
      <div class="form-group mb-3">
        <label class="form-label h-label-required" :for="buildInputId('name')">
          收件人姓名
          <span class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <input class="form-control"
            v-model="modelValue.name"
            :id="buildInputId('name')"
            :name="buildInputName('name')"
            pattern="^[\u4e00-\u9fa5]{2,5}$|^[a-zA-Z]{4,10}$"
            required autocomplete="name">
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="form-group mb-3">
        <label class="form-label h-label-required" :for="buildInputId('mobile')">
          收件人手機
          <span class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <input class="form-control"
            v-model="modelValue.mobile"
            :id="buildInputId('mobile')"
            :name="buildInputName('mobile')"
            pattern="09\d{8}"
            required autocomplete="tel">
        </div>
      </div>
    </div>
  </div>

  <div class="row" ref="citySelector">
    <div class="col-12 col-sm-4">
      <div class="form-group mb-3">
        <label class="form-label h-label-required" :for="buildInputId('state')">
          縣市
          <span class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <select class="form-select"
            data-city
            v-model="modelValue.state"
            :id="buildInputId('state')"
            :name="buildInputName('state')"
            required
            autocomplete="state"></select>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-4">
      <div class="form-group mb-3">
        <label class="form-label h-label-required" :for="buildInputId('city')">
          區
          <span class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <select class="form-select"
            data-dist
            required
            v-model="modelValue.city"
            :id="buildInputId('city')"
            :name="buildInputName('city')"
            autocomplete="city"></select>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-4">
      <div class="form-group mb-3">
        <label class="form-label h-label-required" :for="buildInputId('postcode')">
          郵遞區號<span class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <input class="form-control"
            v-model="modelValue.postcode"
            :id="buildInputId('postcode')"
            :name="buildInputName('postcode')"
            data-zip
            required
          />
        </div>
      </div>
    </div>
  </div>

  <div class="form-group mb-3">
    <label class="form-label h-label-required" :for="buildInputId('address1')">
      地址
      <span class="text-danger"> *</span>
    </label>

    <div class="position-relative">
      <input class="form-control"
        :id="buildInputId('address1')"
        :name="buildInputName('address1')"
        v-model="modelValue.address1" required>
    </div>
  </div>

  <div class="form-check">
    <input type="checkbox" class="form-check-input"
      :id="buildInputId('save')"
      :name="buildInputName('save')">
    <label :for="buildInputId('save')" class="for-check-label">
      儲存為常用地址
    </label>
  </div>
</div>
</template>

<style scoped>

</style>
