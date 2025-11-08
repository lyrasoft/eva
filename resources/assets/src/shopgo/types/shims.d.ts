// import { ComponentCustomProperties } from 'vue'
import { __ } from '@windwalker-io/unicorn-next';
import { CurrencyFormatOptions, useCurrency } from '~shopgo/services';

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $lang: typeof __;
    $priceOffset: (num: number | string, method: 'fixed' | 'offsets' | 'percentage' | string) => string;
    $offsetFormat: (num: number | string, prefix?: string) => string;
    $numberFormat: (num: number | string, prefix?: string) => string;
    $formatPrice: (value: number | string, options?: CurrencyFormatOptions) => string;
    $currency: ReturnType<typeof useCurrency>;
  }
}

declare module '*.scss' {
  const content: { [className: string]: string }
  export default content
}

declare module '*.scss?inline' {
  export default string;
}

declare module '*.css?inline' {
  export default string;
}

declare module '*.vue' {
  import { defineComponent } from 'vue'
  const component: ReturnType<typeof defineComponent>
  export default component;
}
