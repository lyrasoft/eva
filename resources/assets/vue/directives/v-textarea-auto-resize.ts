import type { Directive } from 'vue';

export default <Directive> {
  updated(el, binding) {
    let contentHeight = el.scrollHeight;

    binding.value = binding.value || {};

    const minHeight = binding.value.minHeight;
    
    if (minHeight) {
      contentHeight = contentHeight < minHeight
        ? minHeight
        : contentHeight
    }

    const maxHeight = binding.value.maxHeight;
    
    if (maxHeight) {
      if (contentHeight > maxHeight) {
        contentHeight = maxHeight;
        // this.styles.maxHeight = true;
      } else {
        // this.styles.maxHeight = false;
      }
    }

    const heightVal = contentHeight + 'px';
    el.style.height = `${heightVal}`;
  }
}
