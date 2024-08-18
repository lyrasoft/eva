<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Application\ServiceAwareInterface;
use Windwalker\Form\Field\AbstractField;

trait LayoutFormkitTrait
{
    /**
     * getFormField
     *
     * @param  ServiceAwareInterface  $app  *
     *
     * @return  AbstractField
     *
     * @since  __DEPLOY_VERSION__
     */
    public function toFormField(ServiceAwareInterface $app): AbstractField
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
