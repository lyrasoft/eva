<?php

declare(strict_types=1);

namespace App\Payment;

use App\Data\EventAttendingStore;
use App\Entity\EventOrder;
use Windwalker\Core\Application\AppContext;
use Windwalker\Utilities\Contract\LanguageInterface;

interface EventPaymentInterface
{
    public static function getId(): string;

    public static function getTitle(LanguageInterface $lang): string;

    public static function getDescription(LanguageInterface $lang): string;

    public function process(EventAttendingStore $store): mixed;

    public function orderInfo(EventOrder $order, iterable $attends): string;

    public function receiveNotify(AppContext $app, EventOrder $order): mixed;

    public function createTransactionNo(EventOrder $order): string;
}
