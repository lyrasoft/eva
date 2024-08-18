<?php

declare(strict_types=1);

namespace App;

use Windwalker\Core\Package\AbstractPackage;
use Windwalker\Core\Package\PackageInstaller;
use Windwalker\Core\Runtime\Config;
use Windwalker\Data\Collection;
use Windwalker\DI\Container;
use Windwalker\DI\ServiceProviderInterface;
use Windwalker\Utilities\Contract\LanguageInterface;

class FormkitPackage extends AbstractPackage implements ServiceProviderInterface
{
    public function __construct(protected Config $config)
    {
    }

    public function register(Container $container): void
    {
        $container->share(static::class, $this);
        $container->prepareSharedObject(FormkitService::class);
    }

    public function install(PackageInstaller $installer): void
    {
    }

    public function config(string $path, string $delimiter = '.', int $depth = 0): mixed
    {
        return $this->getConfig()->getDeep($path, $delimiter, $depth);
    }

    public function getConfig(): Collection
    {
        return $this->config->extract('formkit');
    }

    public function getDefaultExtends(): string
    {
        return $this->config('view.default_extends');
    }

    public function getExtendsOptions(LanguageInterface $lang): array
    {
        $extends = $this->config('view.extends');

        $options = [];

        foreach ($extends as $key => $value) {
            if (is_numeric($key)) {
                $options[$key] = $key;
            } else {
                $text = $value;

                if ($lang->has($value)) {
                    $text = $lang->trans($value);
                }

                $options[$key] = $text;
            }
        }

        return $options;
    }
}
