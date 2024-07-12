<?php

declare(strict_types=1);

namespace App\Payment;

use App\Data\EventAttendingStore;
use App\Entity\EventOrder;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Utilities\Contract\LanguageInterface;

class TransferPayment implements EventPaymentInterface
{
    public function __construct(
        protected ApplicationInterface $app,
        protected ?\Closure $renderHandler
    ) {
        //
    }

    public static function getId(): string
    {
        return 'transfer';
    }

    public static function getTitle(LanguageInterface $lang): string
    {
        return '轉帳付款';
    }

    public static function getDescription(LanguageInterface $lang): string
    {
        return '顯示銀行帳號，轉帳支付';
    }

    public function process(EventAttendingStore $store): mixed
    {
        return null;
    }

    public function runTask(AppContext $app, EventOrder $order): mixed
    {
        return '';
    }

    public function getRenderHandler(): ?\Closure
    {
        return $this->renderHandler;
    }

    public function setRenderHandler(?\Closure $renderHandler): static
    {
        $this->renderHandler = $renderHandler;

        return $this;
    }

    public function orderInfo(EventOrder $order, iterable $attends): string
    {
        $handler = $this->getRenderHandler();

        if (!$handler) {
            return '';
        }

        return $this->app->call(
            $handler,
            [
                EventOrder::class => $order,
                'order' => $order,
                'attends' => $attends,
            ]
        );
    }

    public function createTransactionNo(EventOrder $order): string
    {
        return '';
    }
}
