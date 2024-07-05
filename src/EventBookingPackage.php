<?php

declare(strict_types=1);

namespace App;

use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Core\Package\AbstractPackage;
use Windwalker\Core\Package\PackageInstaller;
use Windwalker\DI\Container;
use Windwalker\DI\ServiceProviderInterface;

class EventBookingPackage extends AbstractPackage implements ServiceProviderInterface
{
    public function __construct(protected ApplicationInterface $app)
    {
        //
    }

    public function register(Container $container): void
    {
        $container->share(static::class, $this);
    }

    public function install(PackageInstaller $installer): void
    {
        //
    }

    public function config(string $name, ?string $delimiter = '.'): mixed
    {
        return $this->app->config('event_booking' . $delimiter . $name, $delimiter);
    }
}
