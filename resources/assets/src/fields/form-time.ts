
import '@main';

export default function (app: any, vue: typeof Vue) {
  const { defineComponent } = vue;

  return defineComponent({
    name: 'FormTime',
    template: `
      
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
