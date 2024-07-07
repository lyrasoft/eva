<?php

declare(strict_types=1);

namespace App\Module\Admin\EventPlan\Form;

use Lyrasoft\Luna\Field\UserModalField;
use Unicorn\Field\CalendarField;
use Unicorn\Field\SwitcherField;
use Windwalker\Form\Field\NumberField;
use Unicorn\Enum\BasicState;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Attributes\Fieldset;
use Windwalker\Form\Attributes\FormDefine;
use Windwalker\Form\Attributes\NS;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Field\HiddenField;
use Windwalker\Form\Form;

class EditForm
{
    use TranslatorTrait;

    #[FormDefine]
    #[NS('item')]
    public function main(Form $form): void
    {
        $form->add('title', TextField::class)
            ->label($this->trans('unicorn.field.title'))
            ->addFilter('trim')
            ->required(true);

        $form->add('id', HiddenField::class);
    }

    #[FormDefine]
    #[Fieldset('basic')]
    #[NS('item')]
    public function basic(Form $form): void
    {
        $form->add('price', NumberField::class)
            ->label('售價')
            ->step('0.01');

        $form->add('origin_price', NumberField::class)
            ->label('原價')
            ->step('0.01');

        $form->add('start_date', CalendarField::class)
            ->label('開始時間');

        $form->add('end_date', CalendarField::class)
            ->label('結束時間');

        $form->add('require_validate', SwitcherField::class)
            ->label('需要審核')
            ->circle(true)
            ->color('primary');

        $form->add('quota', NumberField::class)
            ->label('限額');

        $form->add('once_max', NumberField::class)
            ->label('單次最大購買量');
    }

    #[FormDefine]
    #[Fieldset('meta')]
    #[NS('item')]
    public function meta(Form $form): void
    {
        $form->add('state', SwitcherField::class)
            ->label($this->trans('unicorn.field.published'))
            ->circle(true)
            ->color('success')
            ->defaultValue('1');

        $form->add('created', CalendarField::class)
            ->label($this->trans('unicorn.field.created'))
            ->disabled(true);

        $form->add('modified', CalendarField::class)
            ->label($this->trans('unicorn.field.modified'))
            ->disabled(true);

        $form->add('created_by', UserModalField::class)
            ->label($this->trans('unicorn.field.author'))
            ->disabled(true);

        $form->add('modified_by', UserModalField::class)
            ->label($this->trans('unicorn.field.modified_by'))
            ->disabled(true);

        $form->add('event_id', HiddenField::class);

        $form->add('stage_id', HiddenField::class);
    }
}
