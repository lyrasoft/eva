<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\EventOrder;
use App\EventBookingPackage;
use Lyrasoft\Sequence\Service\SequenceService;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\DI\Attributes\Service;

#[Service]
class EventOrderService
{
    public function __construct(protected ApplicationInterface $app, protected EventBookingPackage $eventBooking)
    {
    }

    public function createNo(EventOrder $order): string
    {
        $orderNo = $this->eventBooking->config('order.no_handler');

        if (!$orderNo instanceof \Closure) {
            throw new \LogicException('Order NO handler is not closure');
        }

        return $this->app->call(
            $orderNo,
            [
                'order' => $order,
                EventOrder::class => $order,
            ]
        );
    }
}
