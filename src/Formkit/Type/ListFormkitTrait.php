<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\DOM\DOMElement;
use Windwalker\Form\Field\ListField;

use function Windwalker\DOM\h;
use function Windwalker\uid;

trait ListFormkitTrait
{
    use TranslatorTrait;

    public function hasOther(): bool
    {
        return (bool) $this->data->enable_other;
    }

    protected function getOtherOption(ListField $field): void
    {
        $field->option(
            $this->getOptionText($field),
            $this->trans('formkit.option.other'),
            ['id' => uid('option'), 'class' => 'c-other-option']
        );
    }

    public function getOptionText(ListField $field): DOMElement
    {
        return h(
            'div',
            ['class' => 'd-flex'],
            [
                h('div', ['class' => 'me-2 text-nowrap'], $this->trans('formkit.option.other')),
                $this->getOtherInput($field),
            ]
        );
    }

    public function getOtherInput(ListField $field): DOMElement
    {
        $newField = clone $field;

        return h('input', [
            'class' => 'c-other-input form-control form-control-sm js-other-text',
            'name' => $newField->setName($field->getName() . '_other')->getInputName(),
        ]);
    }
}
