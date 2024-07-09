<?php

declare(strict_types=1);

use App\Entity\EventAttend;
use App\Entity\EventOrder;
use Lyrasoft\Sequence\Service\SequenceService;

use function EventBooking\priceFormat;

return [
    'event_booking' => [
        'enabled' => true,

        'providers' => [
            \App\EventBookingPackage::class
        ],

        'bindings' => [
            //
        ],

        'fixtures' => [
            'locale' => 'en_US',
        ],

        'order_no' => [
            'handler' => function (EventOrder $order, SequenceService $sequenceService) {
                return $sequenceService->getNextSerialWithPrefix(
                    'event_order',
                    'EVT-' . \Windwalker\now('ym') . '-',
                    5
                );
            }
        ],

        'attend_no' => [
            'handler' => function (EventOrder $order, EventAttend $attend, SequenceService $sequenceService) {
                return $sequenceService->getNextSerialWithPrefix(
                    'event_attend',
                    'A-' . \Windwalker\now('ym') . '-',
                    6
                );
            }
        ],

        'payment_no' => [
            'maxlength' => 20, // Digits length will be maxlength - 9
        ],

        'invoice_no' => [
            'handler' => function (EventOrder $order, SequenceService $sequenceService) {
                return $sequenceService->getNextSerialWithPrefix(
                    'event_invoice',
                    'INV-',
                    11
                );
            }
        ],

        'price_formatter' => static fn(mixed $price) => priceFormat($price, '$')
    ]
];
