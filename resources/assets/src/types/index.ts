import '@windwalker-io/unicorn/src/types';
import useFormkitFieldFunc from '@vue/services/form-mixin';

declare global {
  var Ribble: any;

  interface Window {
    useFormkitField: typeof useFormkitFieldFunc;
  }

  var useFormkitField: typeof useFormkitFieldFunc;

  namespace useFormkitField {
    export var props: any;
  }
}
