<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\Melo\Entity\MeloOrder;
use Lyrasoft\Melo\Enum\EcpayPaymentType;
use Lyrasoft\Melo\Enum\OrderState;
use Lyrasoft\Melo\Features\Payment\EcpayPayment;
use Lyrasoft\Melo\Features\Payment\TransferPayment;
use Lyrasoft\Melo\Features\Question\Boolean\BooleanQuestion;
use Lyrasoft\Melo\Features\Question\MultiSelect\MultiSelectQuestion;
use Lyrasoft\Melo\Features\Question\Select\SelectQuestion;
use Lyrasoft\Melo\Features\Section\Homework\HomeworkSection;
use Lyrasoft\Melo\Features\Section\Quiz\QuizSection;
use Lyrasoft\Melo\Features\Section\Video\VideoSection;
use Lyrasoft\Melo\MeloPackage;
use Lyrasoft\Toolkit\Encode\BaseConvert;
use Windwalker\Core\Attributes\ConfigModule;

use function Lyrasoft\Melo\numberFormat;

return #[ConfigModule('melo', enabled: true, priority: 100, belongsTo: MeloPackage::class)]
static fn() => [
    'providers' => [
        MeloPackage::class,
    ],

    'upload' => [
        'video_max_bitrate' => 2 * 1024 * 1024, // 2Mbps
    ],

    'checkout' => [
        'allow_anonymous' => false,
        'default_expiry' => '+7days',
        'receiver_roles' => ['superuser', 'manager', 'admin'],
    ],

    'order' => [
        'no_handler' => function (MeloOrder $order) {
            return 'LS' . str_pad((string) $order->id, 10, '0', STR_PAD_LEFT);
        },
        'initial_state' => fn () => OrderState::PENDING,
    ],

    'shop' => [
        'sitename' => 'Melo',
        'logo' => 'vendor/lyrasoft/melo/images/simular-logo.png',
    ],

    'upload_profiles' => [
        's3_multipart_storage' => 's3',
        'file' => 'default',
        'image' => 'image',
    ],

    'teachers' => [
        'roles' => ['teacher'],
    ],

    'sections' => [
        'defines' => [
            VideoSection::class,
            HomeworkSection::class,
            QuizSection::class,
        ]
    ],

    'questions' => [
        'defines' => [
            BooleanQuestion::class,
            SelectQuestion::class,
            MultiSelectQuestion::class,
        ]
    ],

    'price_formatter' => [
        'short' => fn(mixed $price) => numberFormat($price, '$'),
        'full' => fn(mixed $price) => numberFormat($price, 'TWD $'),
    ],

    'payment' => [
        'no_handler' => function (MeloOrder $order) {
            // Max length: 20
            $no = $order->no;

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
            'ecpay_credit' => \Windwalker\DI\create(
                EcpayPayment::class,
                type: EcpayPaymentType::CREDIT,
            ),
            'ecpay_atm' => \Windwalker\DI\create(
                EcpayPayment::class,
                type: EcpayPaymentType::ATM
            ),
        ],
    ],
];
