<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\EventAttend;
use App\Entity\EventOrder;
use App\EventBookingPackage;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\DI\Attributes\Service;

#[Service]
class EventAttendeeService
{
    public function __construct(protected ApplicationInterface $app, protected EventBookingPackage $eventBooking)
    {
    }

    public function createNo(EventOrder $order, EventAttend $attend): string
    {
        $handler = $this->eventBooking->config('attend_no.handler');

        if (!$handler instanceof \Closure) {
            throw new \LogicException('Attend NO handler is not closure');
        }

        return $this->app->call(
            $handler,
            [
                'order' => $order,
                'attend' => $attend,
                EventOrder::class => $order,
                EventAttend::class => $attend,
            ]
        );
    }
}
