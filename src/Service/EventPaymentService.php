<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\EventOrder;
use App\EventBookingPackage;
use App\Payment\EventPaymentInterface;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Service;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

use function Windwalker\collect;

#[Service]
class EventPaymentService
{
    use InstanceCacheTrait;

    public function __construct(protected ApplicationInterface $app, protected EventBookingPackage $eventBooking)
    {
    }

    public function getGateway(string $alias): ?EventPaymentInterface
    {
        return $this->getGateways()[$alias] ?? null;
    }

    /**
     * @return  Collection<EventPaymentInterface>
     */
    public function getGateways(bool $refresh = false): Collection
    {
        return $this->once(
            'gateways',
            function () {
                $gateways = (array) $this->eventBooking->config('payment.gateways');

                foreach ($gateways as $i => $gateway) {
                    $gateways[$i] = $this->app->resolve($gateway);
                }

                return collect($gateways);
            },
            $refresh
        );
    }
}
