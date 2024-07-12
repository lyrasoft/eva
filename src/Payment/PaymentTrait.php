<?php

declare(strict_types=1);

namespace App\Payment;

use App\Entity\EventOrder;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\RouteUri;
use Windwalker\DI\Attributes\Inject;

trait PaymentTrait
{
    #[Inject]
    protected Navigator $nav;

    public function getReceiveEndpoint(EventOrder $order): RouteUri
    {
        return $this->nav->to('event_receive_notify')->id($order->getId())->full();
    }
}
