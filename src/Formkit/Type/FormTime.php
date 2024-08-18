<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Application\ServiceAwareInterface;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\TimeField;
use Windwalker\Utilities\Contract\LanguageInterface;

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
    public static function getTitle(): string
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

    public static function getGroup(LanguageInterface $lang): string
    {
        return '文字輸入';
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
     * @param  ServiceAwareInterface  $app  *
     *
     * @return  AbstractField
     *
     * @since  __DEPLOY_VERSION__
     */
    public function toFormField(ServiceAwareInterface $app): AbstractField
    {
        return (new TimeField($this->getLabel(), $this->getLabel()));
    }
}
