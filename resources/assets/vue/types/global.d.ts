import useFormkitFieldFunc from '@vue/services/form-mixin';

declare global {
  interface Window {
    useFormkitField: typeof useFormkitFieldFunc;
  }

  var useFormkitField: typeof useFormkitFieldFunc;

  namespace useFormkitField {
    export var props: any;
  }
}
