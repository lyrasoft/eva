
import '@main';

export default function (app: any, vue: typeof Vue) {
  const { defineComponent } = vue;

  return defineComponent({
    name: 'FormText',
    template: `
      <div class="mt-3 row align-items-center">
        <!-- Subtype -->
        <div class="col-lg-2">
          <label :for="getId('subtype')" class="form-label text-nowrap">限定格式</label>
          <select :id="getId('subtype')" v-model="item.subtype" class="form-select">
            <option value="text">一般文字</option>
            <option value="number">數字</option>
            <option value="email">Email</option>
            <option value="url">網址</option>
            <option value="tel">電話</option>
          </select>
        </div>

        <!-- Pattern -->
        <div class="col-lg-4">
          <label :for="getId('pattern')" class="form-label text-nowrap">
            字串檢查語法 (Regex)
          </label>
          <input :id="getId('pattern')" type="text" class="form-control" v-model="item.pattern" />
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
