import '@main';

export default function (app: any, vue: typeof Vue) {
  const { defineComponent } = vue;

  return defineComponent({
    name: 'FormCheckboxes',
    template: `
      <div class="mt-3">
        <SelectOptions v-model="item" icon="far fa-square" other />
      </div>
    `,
    props: {
      ...useFormkitField.props
    },
    setup(props, ctx) {
      const mixin = useFormkitField(props, ctx);

      return {
        ...mixin
      };
    },
  });
}
