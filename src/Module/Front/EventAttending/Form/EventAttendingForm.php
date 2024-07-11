<?php

declare(strict_types=1);

namespace App\Module\Front\EventAttending\Form;

use App\Enum\InvoiceType;
use Unicorn\Field\ButtonRadioField;
use Unicorn\Field\InlineField;
use Windwalker\Form\Attributes\Fieldset;
use Windwalker\Form\Attributes\FormDefine;
use Windwalker\Form\Attributes\NS;
use Windwalker\Form\Field\RadioField;
use Windwalker\Form\Field\TelField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Form;

class EventAttendingForm
{
    #[FormDefine]
    #[Fieldset('payer')]
    #[NS('order')]
    public function payer(Form $form): void
    {
        $form->add('name', TextField::class)
            ->label('付款者姓名')
            ->required(true);

        $form->add('email', TextField::class)
            ->label('付款者 Email')
            ->required(true);

        $form->add('mobile', TelField::class)
            ->label('付款者手機')
            ->pattern('09\d{8}')
            ->help('格式: 09 開頭共 10 碼數字，不加 -');

        $form->add('invoice_type', RadioField::class)
            ->label('發票類型')
            ->registerFromEnums(InvoiceType::class)
            ->defaultValue(InvoiceType::PERSONAL);

        $form->add('invoice_data/carrier_code', TextField::class)
            ->set('showon', ['order/invoice_type' => InvoiceType::PERSONAL->value])
            ->label('載具編號')
            ->pattern('\/[0-9A-Z\.-+]{7}');

        $form->add('invoice_data/title', TextField::class)
            ->label('發票抬頭')
            ->set('showon', ['order/invoice_type' => InvoiceType::BUSINESS->value])
            ->required(true);

        $form->add('invoice_data/vat', TextField::class)
            ->label('發票統編')
            ->set('showon', ['order/invoice_type' => InvoiceType::BUSINESS->value])
            ->required(true);
    }
}
