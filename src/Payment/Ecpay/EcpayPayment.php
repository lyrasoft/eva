<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Payment\Ecpay;

use Lyrasoft\ShopGo\Field\OrderStateListField;
use Lyrasoft\ShopGo\Payment\AbstractPayment;
use Windwalker\Core\Language\LangService;
use Windwalker\Form\Attributes\Fieldset;
use Windwalker\Form\Field\CheckboxesField;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\NumberField;
use Windwalker\Form\Field\SpacerField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Form;

/**
 * The EcpayPayment class.
 */
class EcpayPayment extends AbstractPayment
{
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
            $form->ns(
                'payment',
                #[Fieldset('payment', '支付參數')]
                function (Form $form) {
                    $form->add('merchant_id', TextField::class)
                        ->label('MerchantID')
                        ->placeholder(env('ECPAY_PAYMENT_MERCHANT_ID', '2000132'));

                    $form->add('hash_key', TextField::class)
                        ->label('HashKey')
                        ->placeholder(env('ECPAY_PAYMENT_HASH_KEY', '5294y06JbISpM5x9'));

                    $form->add('hash_iv', TextField::class)
                        ->label('HashIV')
                        ->placeholder(env('ECPAY_PAYMENT_HASH_IV', 'v77hoKGq4kWxNNIS'));

                    $form->add('gateway', ListField::class)
                        ->label('支付方式')
                        ->option('ATM 虛擬帳戶繳款', 'ATM')
                        ->option('超商條碼繳款', 'Barcode')
                        ->option('CVS 超商代碼繳款', 'CVS')
                        ->option('WebATM 繳款', 'WebAtm')
                        ->option('Android Pay', 'AndroidPay')
                        ->option('信用卡繳款', 'Creditcard');

                    $form->add('installment', CheckboxesField::class)
                        ->label('信用卡分期')
                        ->option('三期', '3')
                        ->option('六期', '6')
                        ->option('十二期', '12')
                        ->option('十八期', '18')
                        ->option('二十四期', '24')
                        ->set('showon', ['params/payment/gateway' => 'Creditcard'])
                        ->wrapperAttr('data-novalidate', true);

                    $form->add('hr1', SpacerField::class)
                        ->hr(true);

                    $form->add('unpaid_state', OrderStateListField::class)
                        ->label('等待付款狀態')
                        ->defaultValue(15);

                    $form->add('failure_state', OrderStateListField::class)
                        ->label('失敗狀態')
                        ->defaultValue(4);
                }
            );

            $form->ns(
                'invoice',
                #[Fieldset('invoice', '電子發票參數')]
                function (Form $form) {
                    $form->add('merchant_id', TextField::class)
                        ->label('MerchantID')
                        ->placeholder(env('ECPAY_INVOICE_MERCHANT_ID', '2000132'));

                    $form->add('hash_key', TextField::class)
                        ->label('HashKey')
                        ->placeholder(env('ECPAY_INVOICE_HASH_KEY', '5294y06JbISpM5x9'));

                    $form->add('hash_iv', TextField::class)
                        ->label('HashIV')
                        ->placeholder(env('ECPAY_INVOICE_HASH_IV', 'v77hoKGq4kWxNNIS'));

                    $form->add('tax_type', ListField::class)
                        ->label('課稅類別')
                        ->option('應稅', '1')
                        ->option('零稅率', '2')
                        ->option('免稅', '3')
                        ->option('混和應稅與免稅', '9')
                        ->help('課稅類別。若為混合應稅與免稅時 (限收銀機發票無法分辨時使用，需通過申請核可)');

                    $form->add('inv_type', ListField::class)
                        ->label('字軌類別')
                        ->option('一般稅額', '07')
                        ->option('特種稅額', '08');

                    $form->add('delay_day', NumberField::class)
                        ->label('延遲天數')
                        ->min(0)
                        ->help('當天數為 0 時，則付款完成後立即開立發票。');
                }
            );
        });
    }
}
