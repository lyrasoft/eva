import { useTomSelect } from '@windwalker-io/unicorn-next';
import { nextTick, ObjectDirective } from 'vue';

export const vTomSelect: ObjectDirective = {
  async mounted(el, { value }) {
    await nextTick();
    const inc = await useTomSelect(el, value);
  },
  async beforeUnmount(el) {
    const inc = await useTomSelect(el);

    inc.destroy();
  }
};

