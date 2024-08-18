<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Application\Context\AppRequestInterface;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Data\Collection;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\TextField;
use Windwalker\Utilities\Contract\LanguageInterface;

use function Windwalker\collect;

abstract class AbstractFormType
{
    protected Collection $data;

    public function __construct(mixed $data)
    {
        $this->data = collect($data);
    }

    abstract public static function getTitle(): string;

    abstract public static function getId(): string;

    abstract public static function getIcon(): string;

    abstract public static function getDescription(): string;

    public static function getGroup(LanguageInterface $lang): string
    {
        return '';
    }

    public function getLabel(): string
    {
        return $this->data->label;
    }

    public function getFormField(): AbstractField
    {
        return new TextField($this->getLabel(), $this->getLabel());
    }

    public function prepareStore(array $data, AppRequestInterface $request, string $control): array
    {
        return $data;
    }

    public function prepareView(array $data, array $content): array
    {
        $data[$this->getLabel()] = $content[$this->getLabel()] ?? '';

        return $data;
    }

    public static function getDefaultParams(): array
    {
        return [
            'type' => static::getId(),
            'label' => '',
            'description' => '',
            'help' => '',
            'required' => '',
            'validation' => '',
            'readonly' => false,
            'disabled' => false,
            'class' => '',
            'grid_preview' => '0'
        ];
    }

    public function getData(): Collection
    {
        return $this->data;
    }

    public function setData($data): static
    {
        $this->data = $data;

        return $this;
    }

    public static function getTypeMeta(AppContext $app, AssetService $asset, LanguageInterface $lang): array
    {
        return [
            'id' => static::getId(),
            'title' => static::getTitle($lang),
            'group' => static::getGroup($lang),
            'icon' => static::getIcon(),
            'params' => static::getDefaultParams(),
            'description' => static::getDescription($lang),
            'componentName' => static::getVueComponentName(),
            'componentModuleUrl' => static::loadVueComponent($app, $asset),
        ];
    }

    public static function getVueComponentName(): string
    {
        return 'form-' . static::getId();
    }

    public static function loadVueComponent(AppContext $app, AssetService $asset): ?string
    {
        return $asset->path('js/fields/form-' . static::getId() . '.js');
    }
}
