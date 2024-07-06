<?php

declare(strict_types=1);

namespace App\Data;

use Windwalker\Data\ValueObject;

use function Windwalker\uid;

class EventOrderTotal extends ValueObject
{
    public string $id = '';

    public string $type = '';

    public string $title = '';

    public string $code = '';

    public float $value = 0.0;

    public bool $protect = false;

    public array $params = [];

    public function __construct(mixed $data = null)
    {
        $this->id = uid();

        parent::__construct($data);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function isProtect(): bool
    {
        return $this->protect;
    }

    public function setProtect(bool $protect): static
    {
        $this->protect = $protect;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): static
    {
        $this->params = $params;

        return $this;
    }
}
