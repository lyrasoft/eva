<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Shipping\Ecpay;

use Lyrasoft\ShopGo\Cart\CartData;
use Lyrasoft\ShopGo\Cart\Price\PriceObject;
use Lyrasoft\ShopGo\Field\OrderStateListField;
use Lyrasoft\ShopGo\Shipping\AbstractShipping;
use Unicorn\Field\ButtonRadioField;
use Unicorn\Field\SwitcherField;
use Windwalker\Core\Language\LangService;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\NumberField;
use Windwalker\Form\Field\SpacerField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Form;

/**
 * The EcpayShipping class.
 */
class EcpayShipping extends AbstractShipping
{
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

    public function getShippingFeeComputer(CartData $cartData, PriceObject $total): \Closure
    {
        return function () {
            //
        };
    }

    public function define(Form $form): void
    {
        $form->ns('params', function (Form $form) {
            $form->fieldset('shipping')
                ->title('物流參數')
                ->register(
                    function (Form $form) {
                        $form->add('merchant_id', TextField::class)
                            ->label('MerchantID')
                            ->placeholder(env('ECPAY_SHIPPING_MERCHANT_ID', '2000132'));

                        $form->add('hash_key', TextField::class)
                            ->label('HashKey')
                            ->placeholder(env('ECPAY_SHIPPING_HASH_KEY', '5294y06JbISpM5x9'));

                        $form->add('hash_iv', TextField::class)
                            ->label('HashIV')
                            ->placeholder(env('ECPAY_SHIPPING_HASH_IV', 'v77hoKGq4kWxNNIS'));

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

                        $form->add('mart_max_amount', NumberField::class)
                            ->label('超商取貨最大金額')
                            ->min(0)
                            ->defaultValue(19999);

                        $form->add('mart_min_amount', NumberField::class)
                            ->label('超商取貨最小金額')
                            ->min(0)
                            ->defaultValue(0);
                    }
                );
        });
    }
}
