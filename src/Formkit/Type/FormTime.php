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
    public static function getIcon(): string
    {
        return 'far fa-clock';
    }

    public static function getTitle(): string
    {
        return '時間';
    }

    public static function getId(): string
    {
        return 'time';
    }

    public static function getGroup(LanguageInterface $lang): string
    {
        return '文字輸入';
    }

    public static function getDescription(): string
    {
        return '時間選擇器';
    }

    public static function getDefaultParams(): array
    {
        return array_merge(
            parent::getDefaultParams(),
            [
                //
            ]
        );
    }

    public function toFormField(ServiceAwareInterface $app): AbstractField
    {
        return $app->make(TimeField::class)
            ->label($this->getLabel())
            ->setName($this->getLabel());
    }
}
