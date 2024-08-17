<?php

declare(strict_types=1);

namespace App;

use App\Formset\FormkitService;
use Windwalker\Core\Package\AbstractPackage;
use Windwalker\Core\Package\PackageInstaller;
use Windwalker\Core\Runtime\Config;
use Windwalker\Data\Collection;
use Windwalker\DI\Container;
use Windwalker\DI\ServiceProviderInterface;

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
}
