<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\TextareaField;

/**
 * The FormsetText class.
 *
 * @since  __DEPLOY_VERSION__
 */
class FormTextarea extends AbstractFormType
{
    /**
     * getName
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getName(): string
    {
        return '多行文字';
    }

    /**
     * getIcon
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getIcon(): string
    {
        return 'far fa-align-left';
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
        return 'textarea';
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
        return '單行文字欄位';
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
                'height' => '5',
                'placeholder' => '請填寫此欄位'
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
        return (new TextareaField($this->getLabel(), $this->getLabel()))
            ->rows($this->data->height);
    }
}
