<?php

declare(strict_types=1);

namespace App\Payment;

use App\Data\EventAttendingStore;
use App\Entity\EventOrder;
use Lyrasoft\Toolkit\Encode\BaseConvert;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Utilities\Contract\LanguageInterface;

class EcpayPayment implements EventPaymentInterface
{
    use PaymentTrait;
    use ORMAwareViewModelTrait;

    public static function getId(): string
    {
        return 'ecpay';
    }

    public static function getTitle(LanguageInterface $lang): string
    {
        return '綠界支付';
    }

    public static function getDescription(LanguageInterface $lang): string
    {
        return '綠界金流 All-in-one 支付';
    }

    public function process(EventAttendingStore $store): mixed
    {
        /** @var EventOrder $order */
        $order = $store->getOrder();

        $order->setTransactionNo($this->createTransactionNo($order));

        $this->orm->updateOne($order);

        return 'Process Payment';
    }

    public function runTask(AppContext $app, EventOrder $order): mixed
    {
        return 'OK|1';
    }

    public function orderInfo(EventOrder $order, iterable $attends): string
    {
        return '';
    }

    public function createTransactionNo(EventOrder $order): string
    {
        // Max length: 20
        $no = 'P' . str_pad((string) $order->getId(), 13, '0', STR_PAD_LEFT);

        if (WINDWALKER_DEBUG) {
            $no .= 'T' . BaseConvert::encode(time(), BaseConvert::BASE62);
        }

        return $no;
    }
}
