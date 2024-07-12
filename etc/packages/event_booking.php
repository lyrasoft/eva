<?php

declare(strict_types=1);

use App\Entity\EventAttend;
use App\Entity\EventOrder;
use App\Payment\TransferPayment;
use Lyrasoft\Sequence\Service\SequenceService;

use Lyrasoft\Toolkit\Encode\BaseConvert;

use Windwalker\Core\Renderer\RendererService;

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

        'order' => [
            'no_handler' => function (EventOrder $order, SequenceService $sequenceService) {
                return $sequenceService->getNextSerialWithPrefix(
                    'event_order',
                    'EVT-' . \Windwalker\now('ym') . '-',
                    5
                );
            }
        ],

        'attends' => [
            'no_handler' => function (EventOrder $order, EventAttend $attend, SequenceService $sequenceService) {
                return $sequenceService->getNextSerialWithPrefix(
                    'event_attend',
                    'A-' . \Windwalker\now('ym') . '-',
                    6
                );
            }
        ],

        'invoice' => [
            'no_handler' => function (EventOrder $order, SequenceService $sequenceService) {
                return $sequenceService->getNextSerialWithPrefix(
                    'event_invoice',
                    'INV-',
                    11
                );
            }
        ],

        'price_formatter' => static fn(mixed $price) => priceFormat($price, '$'),

        'payment' => [
            'no_handler' => function (EventOrder $order) {
                // Max length: 20
                $no = 'P' . str_pad((string) $order->getId(), 13, '0', STR_PAD_LEFT);

                if (WINDWALKER_DEBUG) {
                    $no .= 'T' . BaseConvert::encode(time(), BaseConvert::BASE62);
                }

                return $no;
            },

            'gateways' => [
                'transfer' => \Windwalker\DI\create(
                    TransferPayment::class,
                    renderHandler: function () {
                        return <<<TEXT
                        銀行帳戶: (800) 123123123
                        TEXT;
                    }
                )
            ]
        ]
    ]
];
