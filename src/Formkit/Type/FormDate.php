<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Application\ServiceAwareInterface;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\DateField;
use Windwalker\Form\Field\DatetimeLocalField;
use Windwalker\Form\Field\TextField;
use Windwalker\Utilities\Contract\LanguageInterface;

/**
 * The FormsetText class.
 *
 * @since  __DEPLOY_VERSION__
 */
class FormDate extends AbstractFormType
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
        return 'far fa-calendar';
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
        return '日期';
    }

    public static function getGroup(LanguageInterface $lang): string
    {
        return '文字輸入';
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
        return 'date';
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
        return '日期選擇器';
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
        return $app->make(DateField::class)
            ->label($this->getLabel())
            ->setName($this->getLabel());
    }

    /**
     * prepareView
     *
     * @param array $data
     * @param array $content
     *
     * @return  array
     *
     * @throws \Exception
     * @since  __DEPLOY_VERSION__
     */
    public function prepareView(array $data, array $content): array
    {
        $data = parent::prepareView($data, $content);

        $data[$this->getLabel()] = Chronos::toFormat($data[$this->getLabel()], 'Y/m/d');

        return $data;
    }
}
