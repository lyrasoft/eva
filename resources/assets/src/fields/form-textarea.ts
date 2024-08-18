
import '@main';

export default function (app: any, vue: typeof Vue) {
  const { defineComponent } = vue;

  return defineComponent({
    name: 'FormTextarea',
    template: `
      <div class="mt-3 row row-cols-lg-auto align-items-center">
        <div class="col-lg-2">
          <label :for="getId('height')" class="form-label">高度</label>
          <div :id="getId('height')" class="input-group">
            <input type="number" class="form-control" v-model="item.height"
              min="1"
            />
            <div class="input-group-text">
              行
            </div>
          </div>
        </div>
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
