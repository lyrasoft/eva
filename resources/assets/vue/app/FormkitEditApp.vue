<script setup lang="ts">
import FieldCard from '@vue/components/FieldCard.vue';
import { prepareList, prepareListItem } from '@vue/services/utilities';
import type { Formkit, FormType } from '@vue/types';
import { computed, nextTick, onMounted, provide, ref } from 'vue';

const props = defineProps<{
  fields: any[];
  types: Record<string, FormType>;
  item: Formkit;
  name: string;
}>();

provide('form.types', props.types);

const defaultType = 'text';
const focus = ref(false);
const fields = ref(prepareList(props.fields));

onMounted(() => {
  if (fields.value.length > 0) {
    focus.value = fields.value[0].uid;
  }
});

function addField(i?: number) {
  const field = prepareListItem({}, props.types[defaultType].params);

  if (i != null) {
    fields.value.splice(i + 1, 0, field);
  } else {
    fields.value.push(field);
  }

  nextTick(() => {
    focus.value = field.uid;
  });
}

function copy(item: any, i: number) {
  let newItem = { ...item };
  delete newItem.uid;
  prepareListItem(newItem);

  newItem.label += ' (複製)';

  fields.value.splice(i + 1, 0, newItem);

  nextTick(() => {
    focus.value = newItem.uid;
  });
}

function remove(item: any, i: number) {
  fields.value.splice(i, 1);
}

const content = computed(() => {
  return JSON.stringify(fields.value || []);
});
</script>

<template>
  <div class="l-form-builder">
    <div class="l-form-builder__content">
      <draggable
        v-model="fields"
        handle=".h-handle"
        :animation="150"
      >
        <TransitionGroup name="fade">
          <div v-for="(field, i) of fields" class="c-field" :key="field.uid"
            style="animation-duration: .3s">
            <FieldCard :is-focused="focus === field.uid"
              v-model="fields[i]"
              @focus="focus = field.uid"
              @copy="copy($event, i)"
              @remove="remove($event, i)"
              @add="addField(i)"
            />
          </div>
        </TransitionGroup>
      </draggable>
    </div>

    <div class="l-form-builder__bottom text-center">
      <button type="button" class="btn btn-outline-primary"
        @click="addField()">
        <span class="fa fa-plus"></span>
        增加欄位
      </button>
    </div>

    <div class="d-none">
      <textarea :name="name">{{ content }}</textarea>
    </div>
  </div>
</template>

<style scoped>

</style>
