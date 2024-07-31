<?php

declare(strict_types=1);

namespace App\Module\Admin\IpRule\Form;

use App\Enum\IpRuleKind;
use Lyrasoft\Luna\Field\UserModalField;
use Unicorn\Field\ButtonRadioField;
use Unicorn\Field\CalendarField;
use Unicorn\Field\SwitcherField;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Attributes\Fieldset;
use Windwalker\Form\Attributes\FormDefine;
use Windwalker\Form\Attributes\NS;
use Windwalker\Form\Field\HiddenField;
use Windwalker\Form\Field\TextareaField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Form;

class EditForm
{
    use TranslatorTrait;

    #[FormDefine]
    #[NS('item')]
    public function main(Form $form): void
    {
        $form->add('id', HiddenField::class);

        $form->add('type', TextField::class)
            ->label($this->trans('unicorn.field.type'));
    }

    #[FormDefine]
    #[Fieldset('basic')]
    #[NS('item')]
    public function basic(Form $form): void
    {
        $form->add('kind', ButtonRadioField::class)
            ->label($this->trans('firewall.ip.rule.field.kind'))
            ->registerFromEnums(IpRuleKind::class, $this->lang)
            ->defaultValue(IpRuleKind::BLOCK_LIST)
            ->required(true);

        $form->add('range', TextField::class)
            ->label($this->trans('firewall.ip.rule.field.range'))
            ->required(true)
            ->help(
                $this->trans(
                    'firewall.ip.rule.field.range.help',
                    link: 'https://github.com/lyrasoft/luna-firewall'
                )
            );

        $form->add('note', TextareaField::class)
            ->label($this->trans('firewall.ip.rule.field.note'))
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
