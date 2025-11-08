import { Tooltip } from 'bootstrap';
import { ObjectDirective } from 'vue';

export const vTooltip: ObjectDirective = {
  async mounted(el, { value }) {
    const inc = Tooltip.getOrCreateInstance(el, value);
  },
  updated(el, { value }) {
    const inc = Tooltip.getOrCreateInstance(el, value);

    inc.update();
  },
  beforeUnmount(el) {
    const inc = Tooltip.getOrCreateInstance(el);

    inc.dispose();
  }
};

