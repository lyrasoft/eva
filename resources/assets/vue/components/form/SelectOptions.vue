<script setup lang="ts">
import { prepareListItem } from '@vue/services/utilities';
import type { FormTypeParams } from '@vue/types';

const props = withDefaults(
  defineProps<{
    icon: string;
    other: boolean;
  }>(),
  {
    icon: 'far fa-circle',
    other: false
  }
);

const item = defineModel<FormTypeParams>({
  required: true
});

function addOption(i?: number) {
  const option = prepareListItem({}, {
    text: ''
  });

  if (i == null) {
    item.value.options.push(option);
  } else {
    item.value.options.splice(i + 1, 0, option);
  }
}

function remove(i: number) {
  item.value.options.splice(i, 1);
}
</script>

<template>
  <div>
    <div class="c-field-options">
      <draggable v-model="item.options"
        handle=".h-option-handle"
        :animatio="150"
        :group="`field-${item.uid}-group`">
        <TransitionGroup name="fade">
          <div class="c-field-option d-flex align-items-center gap-2 mb-2"
            v-for="(option, i) of item.options"
            style="animation-duration: .3s"
            :key="option.uid">
            <div class="">
              <span class="h-option-handle far fa-ellipsis-v fa-fw"
                style="cursor: move"></span>
            </div>
            <div class="">
              <span :class="icon"></span>
            </div>
            <div class="flex-grow-1">
              <input type="text" class="form-control form-control-sm" v-model="option.text" />
            </div>
            <div>
              <button type="button" class="btn btn-sm btn-link"
                @click="remove(i)">
                <span class="far fa-times"></span>
              </button>
            </div>
          </div>
        </TransitionGroup>
      </draggable>

      <!-- Other -->
      <div v-if="other && item.enable_other === '1'"
        class="c-field-option d-flex align-items-center mb-2 gap-2">
        <div class="">
          <span class="h-option-handle far fa-fw"></span>
        </div>
        <div class="">
          <span :class="icon"></span>
        </div>
        <div class="flex-grow-1">
          <input type="text" disabled class="form-control form-control-sm bg-light" value="其他，請填寫" />
        </div>
        <div>
          <button type="button" class="btn btn-sm btn-link"
            @click="item.enable_other = '0'">
            <span class="far fa-times"></span>
          </button>
        </div>
      </div>

      <button v-if="item.enable_other === '0'" type="button" class="btn btn-sm btn-outline-secondary"
        @click="item.enable_other = '1'">
        <span class="far fa-plus"></span>
        新增其他
      </button>
    </div>

    <div class="mt-3 text-center">
      <button type="button" class="btn btn-sm btn-secondary"
        @click="addOption()">
        <span class="far fa-plus"></span>
        新增選項
      </button>
    </div>
  </div>
</template>

<style scoped>

</style>
