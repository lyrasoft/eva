<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Shipping\Ecpay;

use Ecpay\Sdk\Factories\Factory;
use Lyrasoft\Luna\User\Password;
use Lyrasoft\ShopGo\Cart\CartData;
use Lyrasoft\ShopGo\Entity\Location;
use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Field\OrderStateListField;
use Lyrasoft\ShopGo\Shipping\AbstractShipping;
use Lyrasoft\ShopGo\Shipping\PriceRangeTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Unicorn\Field\ButtonRadioField;
use Unicorn\Field\SwitcherField;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Core\Http\AppRequest;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Renderer\RendererService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\RouteUri;
use Windwalker\DI\Attributes\Inject;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\NumberField;
use Windwalker\Form\Field\SpacerField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Form;
use Windwalker\Renderer\CompositeRenderer;

/**
 * The EcpayShipping class.
 */
class EcpayShipping extends AbstractShipping
{
    use PriceRangeTrait;

    #[Inject]
    protected ApplicationInterface $app;

    public static function getTypeIcon(): string
    {
        return 'fa fa-truck';
    }

    public static function getTypeTitle(LangService $lang): string
    {
        return '綠界物流';
    }

    public static function getTypeDescription(LangService $lang): string
    {
        return '綠界超商取貨與付款';
    }

    public function define(Form $form): void
    {
        $this->registerPricingForm($form);

        $form->ns('params', function (Form $form) {
            $form->fieldset('shipping')
                ->title('物流參數')
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
                            ->label('貨運方式')
                            ->option('黑貓', 'TCAT')
                            ->option('宅配通', 'ECAN')
                            ->option('全家超商', 'FAMI')
                            ->option('統一超商', 'UNIMART')
                            ->option('萊爾富超商', 'HILIFE');

                        $form->add('hr1', SpacerField::class)
                            ->hr(true);

                        $form->add('sender_name', TextField::class)
                            ->label('寄件人姓名')
                            ->required(true)
                            ->defaultValue('測試人員');

                        $form->add('sender_phone', TextField::class)
                            ->label('寄件人電話')
                            ->required(true)
                            ->defaultValue('55522345');

                        $form->add('sender_cellphone', TextField::class)
                            ->label('寄件人手機')
                            ->required(true)
                            ->defaultValue('0912345678');

                        $form->add('sender_zipcode', TextField::class)
                            ->label('寄件人郵遞區號')
                            ->required(true)
                            ->defaultValue('106');

                        $form->add('sender_address', TextField::class)
                            ->label('寄件人地址')
                            ->required(true)
                            ->defaultValue('台北市測試地址一段1號1F-1');

                        $form->add('hr2', SpacerField::class)
                            ->hr(true);

                        $form->add('shipping_state', OrderStateListField::class)
                            ->label('配送中狀態')
                            ->defaultValue(13);

                        $form->add('delivered_state', OrderStateListField::class)
                            ->label('已送達狀態')
                            ->defaultValue(2);

                        $form->add('received_state', OrderStateListField::class)
                            ->label('已取貨狀態')
                            ->defaultValue(8);

                        $form->add('hr2', SpacerField::class)
                            ->hr(true);

                        $form->add('cvs_type', ButtonRadioField::class)
                            ->label('超商合作類型')
                            ->option('店到店 (C2C)', 'C2C')
                            ->option('大宗寄倉 (B2C)', 'B2C')
                            ->defaultValue('C2C')
                            ->help('備註: 統一超商的店到店稱為【統一超商交貨便】');

                        $form->add('is_collection', SwitcherField::class)
                            ->label('代收貨款')
                            ->circle(true)
                            ->color('primary');

                        $form->add('temperature', ButtonRadioField::class)
                            ->label('溫層')
                            ->option('常溫', '0001')
                            ->option('冷藏', '0002')
                            ->option('冷凍', '0003')
                            ->defaultValue('0001');

                        $form->add('cvs_max_amount', NumberField::class)
                            ->label('超商取貨最大金額')
                            ->min(0)
                            ->defaultValue(19999);

                        $form->add('cvs_min_amount', NumberField::class)
                            ->label('超商取貨最小金額')
                            ->min(0)
                            ->defaultValue(0);
                    }
                );
        });
    }

    public function isSupported(CartData $cartData): bool
    {
        $params = $this->getParams();

        if (static::isCVS($params['gateway'])) {
            $maxAmount = $params['cvs_max_amount'] ?? 19999;
            $minAmount = $params['cvs_min_amount'] ?? 0;

            $total = $cartData->getTotals()['total'];

            return !($total->gt($maxAmount) || $total->lt($minAmount));
        }

        return true;
    }

    public function form(Location $location): string
    {
        $params = $this->getParams();

        $type = $params['gateway'];

        if (!static::isCVS($type)) {
            return '';
        }

        /** @var CompositeRenderer $renderer */
        $renderer = $this->app->service(RendererService::class)->createRenderer();
        $renderer->addPath(WINDWALKER_VIEWS . '/shipping/ecpay');
        $renderer->addPath(__DIR__ . '/views');

        return $renderer->render(
            'form',
            [
                'shipping' => $this,
            ]
        );
    }

    public function prepareOrder(Order $order, CartData $cartData): Order
    {
        $appRequest = $this->app->service(AppRequest::class);
        $checkout = $appRequest->input('checkout');
        $shipping = $checkout['shipping'];

        if ($this->isCVSType()) {
            $data = $order->getShippingData();

            $data['CVSAddress'] = $shipping['CVSAddress'];
            $data['CVSOutSide'] = $shipping['CVSOutSide'];
            $data['CVSStoreID'] = $shipping['CVSStoreID'];
            $data['CVSStoreName'] = $shipping['CVSStoreName'];
            $data['CVSTelephone'] = $shipping['CVSTelephone'];
            $data['LogisticsSubType'] = $shipping['LogisticsSubType'];
        }

        return $order;
    }

    public function processCheckout(Order $order, RouteUri $notifyUrl): UriInterface|ResponseInterface|null
    {
        return null;
    }

    public function orderInfo(Order $order): string
    {
        return '';
    }

    public function runTask(AppContext $app, string $task): mixed
    {
        return match ($task) {
            'mapSelect' => $app->call([$this, 'mapSelect']),
            'mapReply' => $app->call([$this, 'mapReply']),
        };
    }

    public function mapSelect(AppContext $app, Navigator $nav): string
    {
        $params = $this->getParams();
        $ecpay = $this->getEcpay();
        $callback = $app->input('callback');

        $formService = $ecpay->create('AutoSubmitFormWithCmvService');

        $input = [
            'MerchantID' => $this->getMerchantID(),
            'LogisticsType' => 'CVS',
            'LogisticsSubType' => $this->getSubtype(),
            'IsCollection' => $params['is_collection'] ? 'Y' : 'N',

            // 請參考 example/Logistics/Domestic/GetMapResponse.php 範例開發
            'ServerReplyURL' => $reply = (string) $nav->to('shipping_task')
                ->task('mapReply')
                ->id($this->getData()->getId())
                ->var('callback', $callback)
                ->full(),

            'ClientReplyURL' => $reply,
        ];

        $action = $this->getEndpoint('Express/map');

        return $formService->generate($input, $action);
    }

    public function mapReply(AppContext $app): string
    {
        $callback = $app->input('callback');
        $data = json_encode($app->input());

        return <<<HTML
<script>
window.opener.{$callback}($data);
window.close();
</script>
HTML;
    }

    public function getEndpoint(string $path): string
    {
        $stage = $this->isTest() ? '-stage' : '';

        return "https://logistics{$stage}.ecpay.com.tw/" . $path;
    }

    public function isTest(): bool
    {
        return $this->getMerchantID() === '2000132';
    }

    public function getSubtype()
    {
        $params = $this->getParams();
        $gateway = $params['gateway'];

        if ($params['cvs_type'] === 'C2C') {
            return $gateway . 'C2C';
        }

        return $gateway;
    }

    public function isCVSType(): bool
    {
        $params = $this->getParams();

        $type = $params['gateway'];

        return static::isCVS($type);
    }

    public static function isCVS(string $type): bool
    {
        return in_array($type, ['FAMI', 'UNIMART', 'HILIFE'], true);
    }

    public function getMerchantID(): string
    {
        return $this->getParams()['merchant_id'] ?: $this->getEnvCredentials()[0];
    }

    public function getHashKey(): string
    {
        return $this->getParams()['hash_key'] ?: $this->getEnvCredentials()[1];
    }

    public function getHashIV(): string
    {
        return $this->getParams()['hash_iv'] ?: $this->getEnvCredentials()[2];
    }

    public function getEcpay(): Factory
    {
        $params = $this->getParams();

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
            env('ECPAY_SHIPPING_MERCHANT_ID', '2000132'),
            env('ECPAY_SHIPPING_HASH_KEY', '5294y06JbISpM5x9'),
            env('ECPAY_SHIPPING_HASH_IV', 'v77hoKGq4kWxNNIS')
        ];
    }
}
