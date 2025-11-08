<?php

declare(strict_types=1);

namespace App\Subscriber;

use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Cart\CartStorage;
use Lyrasoft\ShopGo\Data\PaymentData;
use Lyrasoft\ShopGo\Data\ShippingData;
use Lyrasoft\ShopGo\Entity\Address;
use Lyrasoft\ShopGo\Enum\InvoiceType;
use Lyrasoft\ShopGo\Event\AfterCheckoutEvent;
use Lyrasoft\ShopGo\Event\BeforeCheckoutEvent;
use Lyrasoft\ShopGo\Payment\PaymentService;
use Lyrasoft\ShopGo\Service\AddressService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Event\Attributes\EventSubscriber;
use Windwalker\Event\Attributes\ListenTo;
use Windwalker\ORM\ORM;

#[EventSubscriber]
class TaiwanCheckoutSubscriber
{
    public function __construct(
        protected ApplicationInterface $app,
        protected ORM $orm,
        protected UserService $userService,
    ) {
    }

    #[ListenTo(BeforeCheckoutEvent::class)]
    public function beforeCheckout(BeforeCheckoutEvent $event): void
    {
        // 強制改用我們的流程，要求 ShopGo 忽略內建流程
        $event->overrideShippingDataProcess = true;
        $event->overridePaymentDataProcess = true;

        $user = $this->userService->getUser();

        // 取得 Taiwan Location ID，依照資料庫內 ID 為主
        $taiwanId = (int) env('MAIN_LOCATION_ID', '212');
        $addressFormat = '{name}, {postcode} {state}{city}{address1}';

        $order = $event->order;
        $input = $event->input;

        // 從 input 取得發票資料，我們原本的版面就直接寫到 input 裡面了
        $receipt = (array) ($input['receipt'] ?? []);
        $shipping = $event->shipping;
        $shippingData = $event->shippingData;
        $payment = $event->payment;
        $paymentData = $event->paymentData;

        // Prepare Payment
        $paymentData->locationId = $taiwanId;
        $paymentData->email = $user->email;
        $paymentData->formatted = AddressService::format(
            $paymentData,
            $addressFormat,
            true
        );

        if ($paymentData->addressId) {
            $address = $this->orm->mustFindOne(Address::class, $paymentData->addressId);
            $paymentData->fillFrom($address);
        } elseif ($paymentData->save) {
            $paymentData = $this->saveAddress($paymentData, $user);
        }

        $order->paymentData = $paymentData;

        // Prepare Shipping
        if ($shippingData->sync) {
            $shippingData->fillFrom($paymentData);
            $shippingData->addressId = $paymentData->addressId;
        }

        $shippingData->locationId = (int) $taiwanId;
        $shippingData->email = $user->email;
        $paymentData->formatted = AddressService::format(
            $paymentData,
            $addressFormat,
            true
        );

        if ($shippingData->addressId) {
            $address = $this->orm->mustFindOne(Address::class, $shippingData->addressId);
            $shippingData->fillFrom($address);
        } elseif ($paymentData->save) {
            $shippingData = $this->saveAddress($shippingData, $user);
        }

        $order->shippingData = $shippingData;

        // Receipt / Invoice
        $invoiceData = $order->invoiceData;

        $order->invoiceType = $receipt['type'];
        $invoiceData->carrierCode = $receipt['carrierCode'] ?? '';

        if ($order->invoiceType === InvoiceType::COMPANY) {
            $invoiceData->vat = $receipt['vat'];
            $invoiceData->title = $receipt['title'];
            $invoiceData->email = $receipt['email'];
        }
    }

    protected function saveAddress(PaymentData|ShippingData $data, User $user): PaymentData|ShippingData
    {
        $address = new Address();
        $address->fillFrom($data);
        $address->type = 'address';
        $address->userId = $user->id;

        $address = $this->orm->createOne(Address::class, $address);

        $data->addressId = $address->id;

        return $data;
    }

    /**
     * 有啟用 transfer 付款方式時，結帳完成後清除購物車資料。
     *
     * 沒有的話不需要這一段，可以移除。
     */
    #[ListenTo(AfterCheckoutEvent::class)]
    public function removeCartDataWhenPaymentIsTransfer(AfterCheckoutEvent $event): void
    {
        $app = $this->app->service(AppContext::class);
        $paymentService = $app->service(PaymentService::class);
        $cartStorage = $app->service(CartStorage::class);
        $order = $event->order;

        $payment = $paymentService->getInstanceById($order->paymentId);

        if ($payment->getData()->type === 'transfer' && !WINDWALKER_DEBUG) {
            $cartStorage->clearChecked();
            $app->state->forget('checkout.data');
        }
    }
}
