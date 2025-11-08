import { useColorPicker } from '@windwalker-io/unicorn-next';
import type SpectrumGlobal from 'spectrum-vanilla';
import type { ObjectDirective } from 'vue';

export const vColorpicker: ObjectDirective<HTMLInputElement> = {
  async mounted(el, { value }) {
    await useColorPicker(el, Object.assign({}, value));
  },
  async updated(el, { value }) {
    await useColorPicker();

    const sp = Spectrum.getInstance(el);

    // el.dispatchEvent(new CustomEvent('change', { detail: {} }));
    
    if (JSON.stringify(value) !== JSON.stringify(sp.options)) {
      sp.rebuild(Object.assign({}, value));
    }
  },
  async unmounted(el) {
    await useColorPicker();
    const sp = Spectrum.getInstance(el);
    sp.destroy();
  }
};

declare global {
  var Spectrum: SpectrumGlobal;
}
