<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Application\Context\AppRequestInterface;
use Windwalker\Core\Http\AppRequest;
use Windwalker\IO\Input;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Contract\LanguageInterface;

/**
 * The FormsetText class.
 *
 * @since  __DEPLOY_VERSION__
 */
class FormGridBoxScale extends FormGridRadioScale
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
        return 'far fa-grip-horizontal';
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
        return '矩陣勾選量表';
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
        return 'grid-box-scale';
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
        return '矩陣勾選量表';
    }

    /**
     * prepareStore
     *
     * @param array        $data
     * @param  AppRequest  $request
     * @param string       $ns
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    public function prepareStore(array $data, AppRequest $request, string $ns): array
    {
        $rows = $data[$this->getLabel()] ?? [];

        $rows = array_map(function ($v) {
            return implode(',', $v);
        }, $rows);
        
        $new = Arr::flatten(
            [
                $this->getLabel() => $rows
            ],
            '_'
        );

        $data = array_merge($data, $new);

        unset($data[$this->getLabel()]);

        return $data;
    }
}
