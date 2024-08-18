<script setup lang="ts">
import type { FormType } from '@vue/types';
import { BDropdownHeader, BDropdown, BDropdownItem } from 'bootstrap-vue-next';
import { computed, inject } from 'vue';

const types = inject<Record<string, FormType>>('form.types')!;

const selected = defineModel<string>({
  required: true
});

const currentType = computed<FormType>(() => {
  return types[selected.value];
});

const groupedTypes = computed(() => {
  const groupedTypes: Record<string, FormType[]> = {};

  for (const k in types) {
    const type = types[k];
    const group = type.group || '__DEFAULT__';

    groupedTypes[group] ??= [];

    groupedTypes[group].push(type);
  }

  return groupedTypes;
});

</script>

<template>
  <BDropdown variant="light" size="lg" class=""
    toggle-class="w-100">
    <template #button-content>
      <i class="fa-fw" :class="currentType.icon"></i>
      {{ currentType.title }}
    </template>

    <template v-for="(types, group) of groupedTypes" :key="group">
      <BDropdownHeader v-if="group !== '__DEFAULT__'">
        {{ group }}
      </BDropdownHeader>

      <BDropdownItem v-for="type of types" :key="type.id"
        :active="selected === type.id"
        @click="selected = type.id"
      >
        <i class="fa-fw" :class="type.icon"></i>
        {{ type.title }}
      </BDropdownItem>
    </template>
  </BDropdown>
</template>

<style scoped>

</style>
