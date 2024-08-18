
import '@main';

export default function (app: any, vue: typeof Vue) {
  const { defineComponent } = vue;

  return defineComponent({
    name: 'FormText',
    template: `
      <div class="mt-3">
        <div class="row gy-3">
          <div class="col-lg-2">
            <label :for="getId('max')" class="mr-2">可選檔案數量</label>
            <input :id="getId('max')" type="number" min="0" v-model="item.max" class="form-control">
          </div>
          <div class="col-lg-5">
            <label :for="getId('accept')" class="mr-2">可接受副檔名（半型逗號分隔）</label>
            <input :id="getId('accept')" type="text" v-model="item.accept" class="form-control">
          </div>
          <div class="col-lg-3">
            <label :for="getId('max_size')" class="mr-2">大小限制 (MB)</label>
            <input :id="getId('max_size')" type="number" v-model="item.max_size" class="form-control">
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
