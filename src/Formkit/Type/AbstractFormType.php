<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Windwalker\Core\Application\Context\AppRequestInterface;
use Windwalker\Data\Collection;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Field\TextField;

use function Windwalker\collect;

abstract class AbstractFormType
{
    protected Collection $data;

    public function __construct(mixed $data)
    {
        $this->data = collect($data);
    }

    abstract public static function getName(): string;

    abstract public static function getId(): string;

    abstract public static function getIcon(): string;

    abstract public static function getDescription(): string;

    public function getTitle(): string
    {
        return static::getName();
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
}
