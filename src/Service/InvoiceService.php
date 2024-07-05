<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\EventOrder;
use App\EventBookingPackage;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\DI\Attributes\Service;

#[Service]
class InvoiceService
{
    public function __construct(protected ApplicationInterface $app, protected EventBookingPackage $eventBooking)
    {
    }

    public function createNo(EventOrder $order): string
    {
        $invoiceNoHandler = $this->eventBooking->config('invoice_no.handler');

        if (!$invoiceNoHandler instanceof \Closure) {
            throw new \LogicException('Invoice NO handler is not closure');
        }

        return $this->app->call(
            $invoiceNoHandler,
            [
                'order' => $order,
                EventOrder::class => $order,
            ]
        );
    }
}
