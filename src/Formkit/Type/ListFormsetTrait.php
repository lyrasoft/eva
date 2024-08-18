<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Field\ListField;

use function Windwalker\DOM\h;
use function Windwalker\uid;

trait ListFormsetTrait
{
    use TranslatorTrait;

    /**
     * getOtherOption
     *
     * @param ListField $field
     *
     * @return void
     *
     * @since  __DEPLOY_VERSION__
     */
    protected function getOtherOption(ListField $field): void
    {
        $field->option(
            $this->getOptionText($field),
            $this->trans('tigcr.formset.option.other'),
            ['id' => uid('option'), 'class' => 'c-other-option']
        );
    }

    public function getOptionText(ListField $field): string
    {
        $newField = clone $field;

        return (string) h(
            'div',
            ['class' => 'd-flex'],
            h('div', ['class' => 'me-2 text-nowrap'], $this->trans('tigcr.formset.option.other'))
            . h('input', [
                'class' => 'c-other-input form-control form-control-sm js-other-text',
                'name' => $newField->setName($field->getLabel() . '_other')->getName()
            ])
        );
    }
}
