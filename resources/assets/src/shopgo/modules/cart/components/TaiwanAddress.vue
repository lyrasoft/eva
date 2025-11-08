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
    countyFieldName: 'checkout[shipping][state]',
    districtFieldName: 'checkout[shipping][city]',
    zipcodeFieldName: 'checkout[shipping][postcode]',
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
        <label class="form-label h-label-required">收件人姓名<span
          class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <input class="form-control"
            v-model="modelValue.name"
            :name="buildInputName('name')"
            pattern="^[\u4e00-\u9fa5]{2,5}$|^[a-zA-Z]{4,10}$"
            required autocomplete="name">
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="form-group mb-3">
        <label class="form-label h-label-required">收件人手機<span
          class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <input class="form-control"
            v-model="modelValue.mobile"
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
        <label class="form-label h-label-required">縣市<span
          class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <select class="form-select"
            data-city
            v-model="modelValue.state"
            :name="buildInputName('state')"
            required
            autocomplete="state"></select>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-4">
      <div class="form-group mb-3">
        <label class="form-label h-label-required">區<span
          class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <select class="form-select"
            data-dist
            required
            v-model="modelValue.city"
            :name="buildInputName('city')"
            autocomplete="city"></select>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-4">
      <div class="form-group mb-3">
        <label class="form-label h-label-required">郵遞區號<span
          class="text-danger"> *</span>
        </label>

        <div class="position-relative">
          <input class="form-control"
            v-model="modelValue.postcode"
            :name="buildInputName('postcode')"
            data-zip
            required
          />
        </div>
      </div>
    </div>
  </div>

  <div class="form-group mb-3">
    <label class="form-label h-label-required">地址<span
      class="text-danger"> *</span>
    </label>

    <div class="position-relative">
      <input class="form-control" :name="buildInputName('address1')" v-model="modelValue.address1" required>
    </div>
  </div>
</div>
</template>

<style scoped>

</style>
