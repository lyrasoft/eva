<?php

declare(strict_types=1);

namespace App\Module\Admin\Comment\Form;

use Lyrasoft\Luna\Field\UserModalField;
use Unicorn\Field\SwitcherField;
use Unicorn\Field\CalendarField;
use Windwalker\Form\Field\TextareaField;
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
            ->addFilter('trim');

        $form->add('id', HiddenField::class);

        $form->add('type', HiddenField::class);
    }

    #[FormDefine]
    #[Fieldset('basic')]
    #[NS('item')]
    public function basic(Form $form): void
    {
        $form->add('content', TextareaField::class)
            ->label('Content')
            ->rows(7);

        $form->add('reply', TextareaField::class)
            ->label('Reply')
            ->rows(7);
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
    }
}
