<script setup lang="ts">
import { data as udata, slideDown, slideUp, uid, useInject } from '@windwalker-io/unicorn-next';
import { watch, ref, computed } from 'vue';
import { Payment } from '~shopgo/types';

const props = defineProps<{
  payment: Payment;
  i: number;
  selected: boolean;
}>();

const emit = defineEmits<{
  (e: 'selected'): void;
}>();

// split state into individual refs
const uidRef = ref(uid());
const checkoutFormComponent = ref<{
  injectId: string | null;
  props: Record<string, any>;
} | null>(props.payment.checkoutFormComponent);
const data = ref({});
const selectedRef = ref(props.selected);
const imageDefault = ref(udata('image.default'));

const PaymentForm = computed(() => {
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
      slideDown(optionLayout.value!);
    } else {
      slideUp(optionLayout.value!);
    }
  }, 0);
});

function onSelected() {
  selectedRef.value = true;

  emit('selected');
}

const optionLayout = ref<HTMLDivElement>();
</script>

<template>
  <div class="card"
    :class="[ selectedRef ? 'border border-primary' : '' ]">
    <div class="card-body d-flex align-items-center gap-3">
      <div class="form-check">
        <input type="radio"
          :id="`input-payment-id-${payment.id}`"
          name="checkout[payment][id]"
          :value="payment.id"
          class="form-check-input"
          @change="onSelected"
          :checked="selectedRef"
        />
        <label :for="`input-payment-id-${payment.id}`"
          class="stretched-link"
          style="cursor: pointer;"
        ></label>
      </div>
      <div class="">
        <div class="ratio ratio-1x1"
          style="width: 45px">
          <img class="object-fit-cover" :src="payment.image || imageDefault" alt="cover">
        </div>
      </div>
      <div>
        <h5 class="m-0">
          {{ payment.title }}
        </h5>
        <div v-if="payment.subtitle" class="text-success">
          {{ payment.subtitle }}
        </div>
      </div>

      <div class="ms-auto">

      </div>
    </div>

    <div v-if="payment.description.trim()" class="card-body border-top ps-5">
      <div class="position-relative" style="z-index: 1"
        v-html="payment.description">

      </div>
    </div>

    <transition name="fade" mode="out-in">
      <div
        ref="optionLayout"
        style="display: none; overflow: hidden; animation-duration: .3s">
        <div v-if="PaymentForm && selectedRef"
          class="card-body border-top">
          <Component :is="PaymentForm" :uid="uidRef" :payment v-bind="formProps" />
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>

</style>
