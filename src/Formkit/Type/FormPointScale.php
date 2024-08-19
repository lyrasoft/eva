<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Application\ServiceAwareInterface;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\RadioField;
use Windwalker\Utilities\Contract\LanguageInterface;

use function Windwalker\uid;

class FormPointScale extends AbstractFormType
{
    /**
     * getIcon
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getIcon(): string
    {
        return 'far fa-ellipsis-h-alt';
    }

    /**
     * getName
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getTitle(): string
    {
        return '分數量表';
    }

    public static function getGroup(LanguageInterface $lang): string
    {
        return '量表';
    }

    /**
     * getName
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getId(): string
    {
        return 'point-scale';
    }

    /**
     * getDescription
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getDescription(): string
    {
        return '分數單選量表';
    }

    public function toFormField(ServiceAwareInterface $app): AbstractField
    {
        return $app->make(RadioField::class)
            ->label($this->getLabel())
            ->setName($this->getLabel());
    }

    /**
     * getDefaultParams
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getDefaultParams(): array
    {
        return array_merge(
            parent::getDefaultParams(),
            [
                'min' => '1',
                'max' => '5',
                'start' => '',
                'end' => '',
            ]
        );
    }
}
