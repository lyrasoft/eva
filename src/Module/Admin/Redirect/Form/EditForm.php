<?php

declare(strict_types=1);

namespace App\Module\Admin\Redirect\Form;

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
use Windwalker\Form\Field\TextareaField;
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
        $form->add('id', HiddenField::class);
    }

    #[FormDefine]
    #[Fieldset('basic')]
    #[NS('item')]
    public function basic(Form $form): void
    {
        $form->add('src', TextField::class)
            ->label($this->trans('firewall.redirect.field.src'))
            ->required(true);

        $form->add('dest', TextField::class)
            ->label($this->trans('firewall.redirect.field.dest'))
            ->required(true);

        $form->add('status', ListField::class)
            ->label($this->trans('firewall.redirect.field.status'))
            ->option('301 Moved Permanently', '301')
            ->option('302 Found', '302')
            ->option('303 See Other', '303')
            ->option('307 Temporary Redirect', '307')
            ->defaultValue('302')
            ->required(true);

        $form->add('params/regex', SwitcherField::class)
            ->label($this->trans('firewall.redirect.field.regex'))
            ->circle(true)
            ->color('warning')
            ->help($this->trans('firewall.redirect.field.regex.help'));

        $form->add('params/not_found_only', SwitcherField::class)
            ->label($this->trans('firewall.redirect.field.404.only'))
            ->circle(true)
            ->color('primary')
            ->help($this->trans('firewall.redirect.field.404.only.help'));

        $form->add('params/handle_lang', SwitcherField::class)
            ->label($this->trans('firewall.redirect.field.handle.lang'))
            ->circle(true)
            ->color('dark')
            ->help($this->trans('firewall.redirect.field.handle.lang.help'));

        $form->add('note', TextareaField::class)
            ->label($this->trans('firewall.redirect.field.note'))
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
