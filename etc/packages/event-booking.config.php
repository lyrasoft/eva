<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\EventBooking\Entity\EventAttend;
use Lyrasoft\EventBooking\Entity\EventOrder;
use Lyrasoft\EventBooking\Enum\AttendState;
use Lyrasoft\EventBooking\Enum\EventOrderState;
use Lyrasoft\EventBooking\EventBookingPackage;
use Lyrasoft\EventBooking\Payment\TransferPayment;
use Lyrasoft\Sequence\Service\SequenceService;
use Lyrasoft\Toolkit\Encode\BaseConvert;
use Windwalker\Core\Attributes\ConfigModule;

use function Lyrasoft\EventBooking\numberFormat;

return #[ConfigModule('event_booking', enabled: true, priority: 100, belongsTo: EventBookingPackage::class)]
static fn() => [
    'providers' => [
        EventBookingPackage::class,
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
                'EVT' . \Windwalker\now('ym') . '-',
                5
            );
        },
        'initial_state' => static fn(EventOrder $order) => EventOrderState::UNPAID,
    ],

    'attends' => [
        'no_handler' => function (EventOrder $order, EventAttend $attend, SequenceService $sequenceService) {
            return $sequenceService->getNextSerialWithPrefix(
                'event_attend',
                'A' . \Windwalker\now('ym') . '-',
                6
            );
        },
        'initial_state' => static fn(EventOrder $order, EventAttend $attend) => AttendState::PENDING,
    ],

    'invoice' => [
        'no_handler' => function (EventOrder $order, SequenceService $sequenceService) {
            return $sequenceService->getNextSerialWithPrefix(
                'event_invoice',
                'INV-',
                11
            );
        },
    ],

    'price_formatter' => static fn(mixed $price) => numberFormat($price, '$'),

    'payment' => [
        'no_handler' => function (EventOrder $order) {
            // Max length: 20
            $no = 'P' . str_pad((string) $order->id, 13, '0', STR_PAD_LEFT);

            if (WINDWALKER_DEBUG) {
                $no .= 'T' . BaseConvert::encode(time(), BaseConvert::BASE62);
            }

            return $no;
        },

        'gateways' => [
            'transfer' => \Windwalker\DI\create(
                TransferPayment::class,
                renderHandler: \Windwalker\raw(
                    fn() => <<<TEXT
                        銀行帳戶: (800) 123123123
                        TEXT
                )
            ),
        ],
    ],
];
