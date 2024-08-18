<script setup lang="ts">

import { prepareListItem } from '@vue/services/utilities';
import type { FormTypeParams } from '@vue/types';
import { remove } from 'dom7';

const props = withDefaults(
  defineProps<{
    icon: string;
  }>(),
  {
    icon: 'far fa-circle',
  }
);

const item = defineModel<FormTypeParams>({
  required: true
});

function addRow(i?: number) {
  const option = prepareListItem({}, {
    text: ''
  });

  if (i == null) {
    item.value.rows.push(option);
  } else {
    item.value.rows.splice(i + 1, 0, option);
  }
}

function removeRow(i: number) {
  item.value.rows.splice(i, 1);
}

function addColumn(i?: number) {
  const option = prepareListItem({}, {
    text: ''
  });

  if (i == null) {
    item.value.columns.push(option);
  } else {
    item.value.columns.splice(i + 1, 0, option);
  }
}

function removeColumn(i: number) {
  item.value.columns.splice(i, 1);
}
</script>

<template>
  <div class="row">
    <!-- Rows -->
    <div class="col-md-6">
      <h5 class="">
        列
      </h5>

      <div class="c-field-options">
        <draggable v-model="item.rows"
          handle=".h-option-handle"
          :animation="150"
          :group="`field-${item.uid}-rows`"
        >
          <TransitionGroup name="fade">
            <div class="c-field-option d-flex align-items-center mb-2 gap-2"
              v-for="(option, i) of item.rows"
              :key="option.uid"
              style="animation-duration: .3s"
            >
              <div class="">
                <span class="h-option-handle far fa-ellipsis-v fa-fw" style="cursor: move"></span>
              </div>
              <div class=" flex-grow-1">
                <input type="text" class="form-control form-control-sm" v-model="option.text" />
              </div>
              <div>
                <button type="button" class="btn btn-sm btn-link"
                  @click="removeRow(i)">
                  <span class="far fa-times"></span>
                </button>
              </div>
            </div>
          </TransitionGroup>
        </draggable>
      </div>

      <div class="mt-3 text-center">
        <button type="button" class="btn btn-sm btn-secondary"
          @click="addRow()">
          <span class="far fa-plus"></span>
          新增列
        </button>
      </div>
    </div>

    <!-- Columns -->
    <div class="col-md-6">
      <h5 class="">
        欄
      </h5>

      <div class="c-field-options">
        <draggable v-model="item.options"
          handle=".h-option-handle"
          :animation="150"
          :group="`field-${item.uid}-columns`">
          <TransitionGroup name="fade">
            <div class="c-field-option d-flex align-items-center mb-2 gap-2"
              v-for="(option, i) of item.columns"
              :key="option.uid"
              style="animation-duration: .3s"
            >
              <div class="">
                <span class="h-option-handle far fa-ellipsis-v fa-fw" style="cursor: move"></span>
              </div>
              <div class="">
                <span :class="icon"></span>
              </div>
              <div class=" flex-grow-1">
                <input type="text" class="form-control form-control-sm" v-model="option.text" />
              </div>
              <div>
                <button type="button" class="btn btn-sm btn-link"
                  @click="removeColumn(i)">
                  <span class="far fa-times"></span>
                </button>
              </div>
            </div>
          </TransitionGroup>
        </draggable>
      </div>

      <div class="mt-3 text-center">
        <button type="button" class="btn btn-sm btn-secondary"
          @click="addColumn()">
          <span class="far fa-plus"></span>
          新增欄
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
