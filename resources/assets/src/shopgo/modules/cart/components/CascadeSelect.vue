<script lang="ts">
export interface CascadeOptions {
  id?: string;
  selected?: string;
  ignoreSelf?: any;
  placeholder?: string;
  placeholders?: string[];
  ajaxUrl?: string;
  ajaxValueField?: string;
  source?: any[];
  labels?: string[];
  labelWidth?: string;
  fieldWidth?: string;
  readonly?: boolean;
  disabled?: boolean;
  valueField?: string;
  textField?: string;
  horizontal?: boolean | null;
  horizontalColWidth?: string | null;
  defaultValue?: any;
  onSelectInit?: (e: CustomEvent) => void;
  onChange?: (e: Event) => void;
  onValueInit?: (e: Event) => void;
}
</script>
<script setup lang="ts">
import { useHttpClient } from '@windwalker-io/unicorn-next';
import { ref, reactive, onMounted, watch, nextTick } from 'vue';

// Props
const props = defineProps<{
  options?: CascadeOptions;
  selectAttrs?: Record<string, any>;
  name?: string;
}>();

const modelValue = defineModel<any[]>({ required: false });

// Emits
const emit = defineEmits<{
  (e: 'change', event: CustomEvent): void;
}>();

// Access global helper `u` (same as original code)
const u = (globalThis as any).u || (window as any).u;

// Default options
const defaultOpt: CascadeOptions = {
  id: 'cascade-select-' + (u && u.uid ? u.uid() : String(Math.random())),
  selected: '',
  ignoreSelf: null,
  placeholder: '- Select -',
  placeholders: [],
  ajaxUrl: '',
  ajaxValueField: 'value',
  source: [],
  labels: [],
  labelWidth: 'col-md-3',
  fieldWidth: 'col',
  readonly: false,
  disabled: false,
  valueField: 'id',
  textField: 'title',
  horizontal: null,
  horizontalColWidth: null,
  defaultValue: '',
  onSelectInit: (e: CustomEvent) => {},
  onChange: (e: Event) => {},
  onValueInit: (e: Event) => {},
};

// Reactive state
const opt = reactive<CascadeOptions>(Object.assign({}, defaultOpt, props.options || {}));
const lists = ref<any[]>([]);
const values = ref<any[]>([]);
const canModify = ref(true);
const loading = ref(false);
const ajaxUrl = ref(opt.ajaxUrl || '');

// Refs for DOM
const root = ref<HTMLElement>();
const selects = ref<HTMLSelectElement[]>([]);

function init() {
  canModify.value = !opt.readonly && !opt.disabled;
  ajaxUrl.value = opt.ajaxUrl || '';
}

async function prepareValues() {
  if (loading.value) {
    return;
  }

  loading.value = true;
  lists.value = [];

  const incoming = (modelValue.value || []).slice().map((v: any) => String(v));
  let vals = [...incoming];
  values.value = [...vals];

  if (vals.length === 0) {
    vals = [null];
  } else {
    vals.unshift(null);
  }

  let lastValue: any = null;

  for (let i in vals) {
    const v = vals[i];
    // loadItems returns Promise of list
    // i is string index; convert to number if needed in loadItems
    // pass i so loadItems can use previous list
    // loadItems may call ajax or source
    // we await each level
    // Note: loadItems expects (parentId, i)
    // use Number(i) when passing to handle source lookup
    const list = await loadItems(v, Number(i));

    if (list && list.length > 0) {
      lists.value.push(list);
    }

    lastValue = v;
  }

  valueInit(root.value, lastValue, vals);

  loading.value = false;

  await nextTick();
  // call selectInit for first select if exists
  if (selects.value && selects.value.length > 0) {
    selectInit(selects.value[0]);
  }
}

function reset() {
  void prepareValues();
}

function getLabel(i: number) {
  return opt.labels[i] || `Level ${i + 1}`;
}

function getId(i: number) {
  return `${opt.id}__level-${i}`;
}

function getListValue(i: number) {
  return values.value[i] || '';
}

function isSelected(i: number, item: any) {
  return String(getListValue(i)) === String(item[opt.valueField]);
}

function getFinalValue() {
  const vs = values.value.slice();

  if (vs.length === 0) {
    return opt.defaultValue;
  }

  const v = vs
    .filter((v2) => v2 != null)
    .filter((v2) => v2 !== '')
    .pop();

  if (v === undefined) {
    return opt.defaultValue;
  }

  return v;
}

function getLevel() {
  return values.value.length;
}

async function onChange(i: number, event: Event) {
  const el = event.target as HTMLSelectElement;

  values.value[i] = el.value;

  // call user provided onChange
  try {
    opt.onChange(event);
  } catch (e) {
    // ignore errors from user callback
  }

  event.stopPropagation();

  const changeEvent = new CustomEvent('change', {
    detail: {
      el,
      component: componentAPI,
      value: el.value,
      path: values.value,
    },
  });

  // dispatch on root element
  root.value?.dispatchEvent(changeEvent);

  // update local model (defineModel)
  modelValue.value = values.value;

  // keep emitting events for backward compatibility
  emit('change', changeEvent);

  if (el.value === '') {
    // Clear child
    lists.value.splice(i + 1);
    values.value.splice(i + 1);
    return;
  }

  // Get child list
  const list = await loadItems(el.value, i);

  // Clear child
  lists.value.splice(i + 1);
  values.value.splice(i + 1);

  if (list && list.length > 0) {
    lists.value.push(list);

    await nextTick();

    // initialize newly added select
    const lastIndex = selects.value.length - 1;
    if (selects.value && selects.value[lastIndex]) {
      selectInit(selects.value[lastIndex]);
    }
  }
}

async function loadItems(parentId: number | undefined, i: number) {
  const { get } = await useHttpClient();

  const res = await get(
    ajaxUrl.value,
    {
      params: {
        [opt.ajaxValueField]: parentId,
        self: opt.ignoreSelf || null,
      },
    }
  );

  return res.data.data;

  // Source
  if (parentId) {
    const prevList = lists.value[i - 1] || [];
    const node = findFromList(prevList, parentId);
    const children = node?.children || [];
    return handleSourceItems(children);
  }

  return handleSourceItems(opt.source || []);
}

function valueInit($select: any, value: any, path: any[]) {
  const event = new CustomEvent('value.init', {
    detail: {
      el: $select,
      component: componentAPI,
      value,
      path,
    },
  });

  root.value?.dispatchEvent(event);
}

function selectInit($select: any) {
  const event = new CustomEvent('select.init', {
    detail: {
      el: $select,
      component: componentAPI,
    },
  });

  opt.onSelectInit(event);

  root.value?.dispatchEvent(event);
}

function handleSourceItems(itemsIn: any[]) {
  return itemsIn.map((item) => {
    return {
      [opt.valueField]: item.value[opt.valueField],
      [opt.textField]: item.value[opt.textField],
      children: item.children,
    };
  }).filter((item) => {
    if (opt.ignoreSelf) {
      return item[opt.valueField] != opt.ignoreSelf;
    }

    return item;
  });
}

function findFromList(itemsIn: any[], value: any) {
  const found = (itemsIn || []).filter((item) => item[opt.valueField] == value);
  return found.shift();
}

function getPlaceholder(i: number) {
  if (opt.placeholders && opt.placeholders[i]) {
    return opt.placeholders[i];
  }

  return opt.placeholder;
}

// componentAPI used in events to emulate `this` reference from original
const componentAPI = {
  opt,
  lists,
  values,
  getFinalValue,
  getLevel,
  getLabel,
  getId,
  getListValue,
  isSelected,
  onChange,
  loadItems,
  valueInit,
  selectInit,
  handleSourceItems,
  findFromList,
  getPlaceholder,
};

// Watch props.modelValue to reset when emptied
watch(modelValue, (v: any) => {
  if (!v || v.length === 0) {
    reset();
  }
}, { deep: true });

// Mounted
onMounted(async () => {
  init();
  await prepareValues();
});

defineExpose({
  prepareValues
})
</script>

<template>
  <div ref="root">
    <div class="form-group row mb-2"
      v-for="(items, i) of lists" :key="items"
      :class="[opt.horizontal ? (opt.horizontalColWidth || 'col') : '']"
      :data-level="i"
    >
      <label :for="getId(i)"
        class="c-cascade-select__label mb-2"
        :class="opt.labelWidth || 'col-md-3'">
        {{ getLabel(i) }}
      </label>

      <div class="col c-cascade-select__input">
        <select :id="getId(i)" :disabled="!canModify"
          class="form-select custom-select"
          :ref="el => (selects[i] = el)"
          @change="onChange(i, $event)"
        >
          <option value="">
            {{ getPlaceholder(i) }}
          </option>
          <option :value="item[opt.valueField]"
            v-for="item of items" :key="item[opt.valueField]"
            :selected="isSelected(i, item)"
          >
            {{ item[opt.textField] }}
          </option>
        </select>
      </div>
    </div>

    <input :name="props.name" type="hidden" :value="getFinalValue()" />
  </div>
</template>

<style scoped>

</style>
