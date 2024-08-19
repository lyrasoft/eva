<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Application\ServiceAwareInterface;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\RadioField;
use Windwalker\Utilities\Contract\LanguageInterface;

use function Windwalker\uid;

class FormRadio extends FormSelect
{
    use ListFormkitTrait;

    /**
     * getIcon
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getIcon(): string
    {
        return 'far fa-dot-circle';
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
        return '單選題';
    }

    public static function getGroup(LanguageInterface $lang): string
    {
        return '選擇';
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
        return 'radio';
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
        return '單選選項';
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
        return (new RadioField($this->getLabel(), $this->getLabel()))
            ->register(
                function (RadioField $field) {
                    foreach ($this->data->options as $opt) {
                        $field->option($opt['text'], $opt['text'], ['id' => uid('option')]);
                    }

                    // if ($this->data->enable_other) {
                    //     $this->getOtherOption($field);
                    // }
                }
            );
    }
}
