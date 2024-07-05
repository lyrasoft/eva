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
                return $sequenceService->getNextSerialAndPadZero(
                    'event_order',
                    'EVT-' . \Windwalker\now('Ymd'),
                    4
                );
            }
        ],

        'payment_no' => [
            'maxlength' => 20, // Digits length will be maxlength - 9
        ],

        'invoice_no' => [
            'handler' => function (EventOrder $order, SequenceService $sequenceService) {
                return $sequenceService->getNextSerialAndPadZero(
                    'event_invoice',
                    'INV-',
                    11
                );
            }
        ],
    ]
];
