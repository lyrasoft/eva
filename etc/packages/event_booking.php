<?php

declare(strict_types=1);

use App\Entity\EventOrder;
use Lyrasoft\Sequence\Service\SequenceService;

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
                    'EVT-' . \Windwalker\now('Ym') . '-',
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
    ]
];
