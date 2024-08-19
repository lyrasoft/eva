
import '@main';

class FormkitHandler {
  constructor(protected el: HTMLElement, protected uid: string) {
    // this.registerValidation();
    // this.replaceIds();
    // this.autoCheckOther();
  }

  registerValidation() {
    this.el.addEventListener('submit', (e) => {
      let invalid = 0;
      let scrollTarget: HTMLElement;
      const $form = e.currentTarget as HTMLFormElement;

      // 驗證選擇列表
      // -----------------------------
      const fields = $form.querySelectorAll<HTMLDivElement>('.input-list-container[required]');

      // 移除前次驗證的資訊
      fields.find('.invalid-tooltip').remove();

      fields.each((i, el) => {
        const $field = $(el);
        const $inputs = $field.find('[type=checkbox], [type=radio]');
        const $other  = $field.find('.js-other-text');
        const checked = $inputs.filter(':checked');

        // 移除前次驗證的資訊
        $field.closest('.form-group').removeClass('has-invalid');

        $inputs.each((i, input) => {
          input.setCustomValidity('');
        });
        $other[0].setCustomValidity('');

        // 確認沒有勾選就產生錯誤提示
        if (checked.length === 0) {
          const text = Phoenix.__('phoenix.message.validation.value.missing');
          const help = `<small class="invalid-tooltip form-control-tooltip d-block">${text}</small>`;

          // 設定 HTML5 驗證結果
          $inputs.each((i, input) => {
            input.setCustomValidity(text);
          });
          $other[0].setCustomValidity(text);

          // 插入提示
          $field.append(help);
          $field.closest('.form-group').addClass('has-invalid');

          // 準備滑動到第一個物件
          if (!scrollTarget) {
            scrollTarget = $field.closest('.form-group');
          }

          invalid++;
        }
      });

      // 驗證矩陣
      // -----------------------------
      const grids = $form.find('.grid-box-scale-field[required], .grid-radio-scale-field[required]');

      // 移除前次驗證的資訊
      grids.find('.invalid-tooltip').remove();

      grids.each((i, el) => {
        const $field = $(el);
        const $rows = $field.find('tbody > tr');
        console.log(el);
        // 移除前次驗證的資訊
        $rows.closest('.form-group').removeClass('has-invalid');

        $rows.each((i, row) => {
          const $row = $(row);
          const $inputs = $row.find('[type=checkbox], [type=radio]');
          const checked = $inputs.filter(':checked');

          // 移除前次驗證的資訊
          $row.closest('.form-group').removeClass('has-invalid');

          $inputs.each((i, input) => {
            input.setCustomValidity('');
          });

          // 確認沒有勾選就產生錯誤提示
          if (checked.length === 0) {
            const text = Phoenix.__('phoenix.message.validation.value.missing');
            const help = `<small class="invalid-tooltip form-control-tooltip d-block">${text}</small>`;

            // 設定 HTML5 驗證結果
            $inputs.each((i, input) => {
              input.setCustomValidity(text);
            });

            // 插入提示
            $row.find('.c-row-text').append(help);
            $field.closest('.form-group').addClass('has-invalid');

            // 準備滑動到第一個物件
            if (!scrollTarget) {
              scrollTarget = $field.closest('.form-group');
            }

            invalid++;
          }
        });
      });

      if (invalid > 0) {
        if (scrollTarget) {
          $('html, body').animate({
            scrollTop: scrollTarget.offset().top - 100
          }, 500);
        }

        e.stopImmediatePropagation();
        e.stopPropagation();
        e.preventDefault();
      }
    });
  }

  replaceIds() {
    this.$element.find('.c-formset-field').each((i, e) => {
      const $this = $(e);
      const id = 'input-' + $this.attr('data-uid');

      $this.find('> [data-form-group]')
        .attr('id', id + '-control')
        .find('> label')
        .attr('id', id + '-label')
        .attr('for', id);
    });
  }

  autoCheckOther() {
    this.$element.find('.c-other-input').on('input', (e) => {
      const $this = $(e.currentTarget);
      $this.closest('.radio, .checkbox').find('.c-other-option').prop('checked', true);
    });
  }
}

u.directive(
  'formkit',
  {
    mounted(el, { value }) {
      u.module<any, HTMLElement>(el, 'formkit', (el) => new FormkitHandler(el, value))
    }
  }
)
