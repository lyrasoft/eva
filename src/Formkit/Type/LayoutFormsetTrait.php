<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Phoenix\Field\LayoutField;
use Windwalker\Form\Field\AbstractField;

/**
 * The LayoutFormsetTrait class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait LayoutFormsetTrait
{
    /**
     * getFormField
     *
     * @return  AbstractField
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getFormField(): AbstractField
    {
        return (new LayoutField($this->getLabel(), $this->getLabel()))
            ->controlAttr('required', (bool) $this->data->required)
            ->controlClass(static::getId() . '-field')
            ->renderLayout(
                '_widget.formset.field.' . static::getId() . '-field',
                [
                    'data' => $this->data
                ],
                'edge'
            );
    }
}
