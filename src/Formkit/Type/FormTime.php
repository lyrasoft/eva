<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\TimeField;

/**
 * The FormsetText class.
 *
 * @since  __DEPLOY_VERSION__
 */
class FormTime extends AbstractFormType
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
        return 'far fa-clock';
    }

    /**
     * getName
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getName(): string
    {
        return '時間';
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
        return 'time';
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
        return '時間選擇器';
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
                //
            ]
        );
    }

    /**
     * getFormField
     *
     * @return  AbstractField
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getFormField(): AbstractField
    {
        return (new TimeField($this->getLabel(), $this->getLabel()));
    }
}
