<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Payment\Ecpay;

use Ecpay\Sdk\Factories\Factory;
use Ecpay\Sdk\Response\VerifiedArrayResponse;
use Ecpay\Sdk\Services\UrlService;
use Lyrasoft\ShopGo\Cart\CartData;
use Lyrasoft\ShopGo\Cart\CartStorage;
use Lyrasoft\ShopGo\Entity\Location;
use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Entity\OrderItem;
use Lyrasoft\ShopGo\Enum\OrderHistoryType;
use Lyrasoft\ShopGo\Field\OrderStateListField;
use Lyrasoft\ShopGo\Payment\AbstractPayment;
use Lyrasoft\ShopGo\Service\OrderService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\RouteUri;
use Windwalker\DI\Attributes\Inject;
use Windwalker\Form\Field\CheckboxesField;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\SpacerField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Form;
use Windwalker\ORM\ORM;

/**
 * The EcpayPayment class.
 */
class EcpayPayment extends AbstractPayment
{
    #[Inject]
    protected AppContext $app;

    public static function getTypeIcon(): string
    {
        return 'fa fa-money-bill-1-wave';
    }

    public static function getTypeTitle(LangService $lang): string
    {
        return '綠界支付';
    }

    public static function getTypeDescription(LangService $lang): string
    {
        return '綠界金流整合功能';
    }

    public function define(Form $form): void
    {
        $form->ns('params', function (Form $form) {
            $form->fieldset('payment')
                ->title('支付參數')
                ->register(
                    function (Form $form) {
                        $form->add('merchant_id', TextField::class)
                            ->label('MerchantID')
                            ->placeholder($this->getEnvCredentials()[0]);

                        $form->add('hash_key', TextField::class)
                            ->label('HashKey')
                            ->placeholder($this->getEnvCredentials()[1]);

                        $form->add('hash_iv', TextField::class)
                            ->label('HashIV')
                            ->placeholder($this->getEnvCredentials()[2]);

                        $form->add('gateway', ListField::class)
                            ->label('支付方式')
                            ->option('ATM 虛擬帳戶繳款', 'ATM')
                            ->option('超商條碼繳款', 'BARCODE')
                            ->option('CVS 超商代碼繳款', 'CVS')
                            ->option('WebATM 繳款', 'WebATM')
                            ->option('Apple Pay', 'ApplePay')
                            ->option('信用卡繳款', 'Credit');

                        $form->add('installment', CheckboxesField::class)
                            ->label('信用卡分期')
                            ->option('三期', '3')
                            ->option('六期', '6')
                            ->option('十二期', '12')
                            ->option('十八期', '18')
                            ->option('二十四期', '24')
                            ->set('showon', ['params/gateway' => 'Credit'])
                            ->wrapperAttr('data-novalidate', true);

                        $form->add('hr1', SpacerField::class)
                            ->hr(true);

                        $form->add('paid_state', OrderStateListField::class)
                            ->label('付款後狀態')
                            ->defaultValue(5);

                        $form->add('failure_state', OrderStateListField::class)
                            ->label('失敗狀態')
                            ->defaultValue(4);
                    }
                );
        });
    }

    public function form(Location $location): string
    {
        // Todo: make installment choose layout
        return '';
    }

    public function prepareOrder(Order $order, CartData $cartData): Order
    {
        return $order;
    }

    public function processCheckout(Order $order, RouteUri $completeUrl): string
    {
        $params = $this->getParams();
        $paymentParams = $params['payment'] ?? [];

        $nav = $this->app->service(Navigator::class);
        $chronos = $this->app->service(ChronosService::class);

        $notify = $nav->to('payment_task')
            ->id($this->getData()->getId())
            ->var('no', $order->getNo())
            ->full();

        $desc = [];

        /** @var OrderItem $orderItem */
        foreach ($order->getOrderItems()->getAttachedEntities() as $orderItem) {
            $desc[] = "{$orderItem->getTitle()} x {$orderItem->getQuantity()}";
        }

        $input = [
            'MerchantID' => $this->getMerchantID(),
            'MerchantTradeNo' => $order->getPaymentNo(),
            'MerchantTradeDate' => $chronos->toLocalFormat('now', 'Y/m/d H:i:s'),
            'PaymentType' => 'aio',
            'TotalAmount' => (int) $order->getTotal(),
            'TradeDesc' => UrlService::ecpayUrlEncode('Shop Checkout'),
            'ItemName' => implode("#", $desc),
            'ReturnURL' => (string) $notify->task('receivePaid'),
            'ClientBackURL' => (string) $notify->task('return'),
            'ChoosePayment' => $paymentParams['gateway'],
            'EncryptType' => 1,

            'ExpireDate' => 7,
            'PaymentInfoURL' => (string) $notify->task('paymentInfo'),
        ];

        if ($paymentParams['gateway'] === 'Credit') {
            if ($paymentParams['installment']) {
                $input['CreditInstallment'] = implode(',', (array) $paymentParams['installment']);
            }
        }

        $factory = $this->getEcpay();
        $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');

        return $autoSubmitFormService->generate(
            $input,
            $this->getEndpoint('Cashier/AioCheckOut/V5')
        );
    }

    public function orderInfo(Order $order): string
    {
        return '';
    }

    public function runTask(AppContext $app, string $task): mixed
    {
        return match ($task) {
            'receivePaid', 'return', 'paymentInfo' => $app->call([$this, $task])
        };
    }

    public function receivePaid(AppContext $app): string
    {
        $factory = $this->getEcpay();
        /** @var VerifiedArrayResponse $checkoutResponse */
        $checkoutResponse = $factory->create(VerifiedArrayResponse::class);

        try {
            $res = $checkoutResponse->get($_POST);
        } catch (\Exception $e) {
            return '0|' . $e->getMessage();
        }

        $orm = $app->service(ORM::class);
        $orderService = $app->service(OrderService::class);

        $no = (string) $app->input('no');
        $order = $orm->findOne(Order::class, compact('no'));

        if (!$order) {
            return '0|No order';
        }

        $params = &$order->getParams();
        $params['payment_notify_error'] = null;

        try {
            if ((string) $res['RtnCode'] === '1') {
                $paidStateId = $this->getParams()['payment']['paid_state'];

                $orderService->transition(
                    $order,
                    $paidStateId,
                    OrderHistoryType::SYSTEM(),
                    '付款成功',
                    true
                );

                // Todo: Create invoice
                // $invoiceService->updateOrderInvoice($order);
            } else {
                $failedStateId = $this->getParams()['payment']['failure_state'];

                $orderService->transition(
                    $order,
                    $failedStateId,
                    OrderHistoryType::SYSTEM(),
                    $res['RtnMsg'] ?? '付款失敗',
                    false
                );
            }
        } catch (\Throwable $e) {
            $params['payment_notify_error'] = $e->getMessage();

            $orm->updateBatch(
                Order::class,
                compact('params'),
                ['id' => $order->getId()]
            );

            return '0|' . $e->getMessage();
        }

        return '1|OK';
    }

    public function return(AppContext $app, Navigator $nav, CartStorage $cartStorage): RouteUri
    {
        $no = (string) $app->input('no');

        $cartStorage->clear();
        $app->state->forget('checkout.data');

        return $nav->to('checkout')
            ->layout('complete')
            ->var('no', $no);
    }

    public function paymentInfo(AppContext $app): string
    {
        $orm = $app->service(ORM::class);

        $no = (string) $app->input('no');

        $order = $orm->mustFindOne(Order::class, compact('no'));

        $order->setPaymentInfo($app->input()->dump());

        $orm->updateOne(Order::class, $order);

        return '1|OK';
    }

    public function isTest(): bool
    {
        return $this->getMerchantID() === '2000132';
    }

    public function getEndpoint(string $path): string
    {
        $stage = $this->isTest() ? '-stage' : '';

        return "https://payment{$stage}.ecpay.com.tw/" . $path;
    }

    public function getMerchantID(): string
    {
        return $this->getParams()['payment']['merchant_id'] ?: $this->getEnvCredentials()[0];
    }

    public function getHashKey(): string
    {
        return $this->getParams()['payment']['hash_key'] ?: $this->getEnvCredentials()[1];
    }

    public function getHashIV(): string
    {
        return $this->getParams()['payment']['hash_iv'] ?: $this->getEnvCredentials()[2];
    }

    public function getEcpay(): Factory
    {
        return new Factory(
            [
                'hashKey' => $this->getHashKey(),
                'hashIv' => $this->getHashIV(),
            ]
        );
    }

    /**
     * @return  string[]
     */
    protected function getEnvCredentials(): array
    {
        return [
            env('ECPAY_PAYMENT_MERCHANT_ID', '2000132'),
            env('ECPAY_PAYMENT_HASH_KEY', '5294y06JbISpM5x9'),
            env('ECPAY_PAYMENT_HASH_IV', 'v77hoKGq4kWxNNIS'),
        ];
    }
}
