<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Application\Context\AppRequestInterface;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\CheckboxesField;
use Windwalker\IO\Input;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Contract\LanguageInterface;

use function Windwalker\h;

/**
 * The FormsetText class.
 *
 * @since  __DEPLOY_VERSION__
 */
class FormCheckboxes extends FormSelect
{
    use ListFormsetTrait;

    /**
     * getIcon
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getIcon(): string
    {
        return 'far fa-check-square';
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
        return '勾選方塊';
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
        return 'checkboxes';
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
        return '多選核取方塊';
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
        return (new CheckboxesField($this->getLabel(), $this->getLabel()))
            ->register(function (CheckboxesField $field) {
                foreach ($this->data->options as $opt) {
                    $field->option($opt['text'], $opt['text'], ['id' => uniqid('option', true)]);
                }

                if ($this->data->enable_other) {
                    $this->getOtherOption($field);
                }
            });
    }

    /**
     * prepareStore
     *
     * @param array                 $data
     * @param  AppRequestInterface  $request
     * @param string                $control
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    public function prepareStore(array $data, AppRequestInterface $request, string $control): array
    {
        $data[$this->getLabel()] = implode(',', $data[$this->getLabel()] ?? []);

        return $data;
    }
}
