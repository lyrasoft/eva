<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Form\Field\ListField;
use Windwalker\Utilities\Classes\StringableInterface;
use function Windwalker\h;

/**
 * The ListFormsetTrait class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait ListFormsetTrait
{
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
            __('tigcr.formset.option.other'),
            ['id' => uniqid('option', true), 'class' => 'c-other-option']
        );
    }

    /**
     * getOptionText
     *
     * @param ListField $field
     *
     * @return  StringableInterface
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getOptionText(ListField $field): StringableInterface
    {
        return new class ($field) implements StringableInterface {
            protected $field;

            public function __construct(ListField $field)
            {
                $this->field = $field;
            }

            public function __toString(): string
            {
                $newField = clone $this->field;

                return (string) h(
                    'div',
                    ['class' => 'd-flex'],
                    h('div', ['class' => 'mr-2 text-nowrap'], __('tigcr.formset.option.other'))
                    . h('input', [
                        'class' => 'c-other-input form-control form-control-sm js-other-text',
                        'name' => $newField->setName($this->field->getLabel() . '_other')->getFieldName(true)
                    ])
                );
            }
        };
    }
}
