<script setup lang="ts">
import { uid, data as udata, slideUp, slideDown, useInject } from '@windwalker-io/unicorn-next';
import { watch, ref, computed } from 'vue';
import { Shipping } from '~shopgo/types';

const props = defineProps<{
  shipping: Shipping;
  i: number;
  selected: boolean;
}>();

const emit = defineEmits<{
  (e: 'selected'): void;
}>()

// split state into independent refs
const uidRef = ref(uid());
const checkoutFormComponent = ref<{
  injectId: string | null;
  props: Record<string, any>;
} | null>(props.shipping.checkoutFormComponent);
const data = ref({});
const selectedRef = ref(props.selected);
const imageDefault = ref(udata('image.default'));

const ShippingForm = computed(() => {
  if (!checkoutFormComponent.value?.injectId) {
    return null;
  }

  return useInject(checkoutFormComponent.value.injectId);
});
const formProps = computed(() => {
  return checkoutFormComponent.value?.props || {};
});


watch(() => props.selected, () => {
  selectedRef.value = props.selected;

  setTimeout(() => {
    if (selectedRef.value) {
      const scripts = form.value!.querySelectorAll('.card-body script');
      for (const script of scripts) {
        eval(script.textContent);
      }

      slideDown(form.value!);
    } else {
      slideUp(form.value!);
    }
  }, 0);
});

function onSelected() {
  selectedRef.value = true;

  emit('selected');
}

const form = ref<HTMLDivElement>();
</script>

<template>
  <div class="card"
    :class="[ selectedRef ? 'border border-primary' : '' ]">
    <div class="card-body d-flex align-items-center gap-3">
      <div class="form-check">
        <input type="radio"
          :id="`input-shipping-id-${shipping.id}`"
          name="checkout[shipping][id]"
          :value="shipping.id"
          class="form-check-input"
          @change="onSelected"
          :checked="selectedRef"
        />
        <label :for="`input-shipping-id-${shipping.id}`"
          class="stretched-link"
          style="cursor: pointer;"
        ></label>
      </div>
      <div class="">
        <div class="ratio ratio-1x1"
          style="width: 45px">
          <img :src="shipping.image || imageDefault" alt="cover">
        </div>
      </div>
      <div>
        <h5 class="m-0">
          {{ shipping.title }}
        </h5>
        <div v-if="shipping.subtitle" class="text-success">
          {{ shipping.subtitle }}
        </div>
      </div>

      <div class="ms-auto">
            <span class="fs-5">
            {{ $formatPrice(shipping.fee, { code: true }) }}
            </span>
      </div>
    </div>

    <div v-if="shipping.description.trim()" class="card-body border-top ps-5">
      <div class="position-relative" style="z-index: 1"
        v-html="shipping.description">

      </div>
    </div>

    <transition name="fade" mode="out-in">
      <div
        ref="form"
        style="display: none; position: relative; z-index: 1; overflow: hidden; animation-duration: .3s">
        <div v-if="ShippingForm && selectedRef"
           class="card-body border-top">
          <Component :is="ShippingForm" :uid="uidRef" :shipping v-bind="formProps" />
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>

</style>
