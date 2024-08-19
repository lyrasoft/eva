<?php

declare(strict_types=1);

namespace App\Module\Admin\Formkit\Form;

use App\FormkitPackage;
use Lyrasoft\Luna\Field\UserModalField;
use Unicorn\Field\CalendarField;
use Unicorn\Field\SwitcherField;
use Unicorn\Field\SingleImageDragField;
use Windwalker\Form\Field\TextareaField;
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

    public function __construct(protected FormkitPackage $formkit)
    {
    }

    #[FormDefine]
    #[NS('item')]
    public function main(Form $form): void
    {
        $form->add('title', TextField::class)
            ->label($this->trans('unicorn.field.title'))
            ->addFilter('trim')
            ->required(true);

        $form->add('alias', TextField::class)
            ->label($this->trans('unicorn.field.alias'))
            ->addFilter('trim');

        $form->add('id', HiddenField::class);
    }

    #[FormDefine]
    #[Fieldset('basic')]
    #[NS('item')]
    public function basic(Form $form): void
    {
        $form->add('state', SwitcherField::class)
            ->label($this->trans('unicorn.field.published'))
            ->circle(true)
            ->color('success')
            ->defaultValue('1');

        $form->add('description', TextareaField::class)
            ->label($this->trans('unicorn.field.description'))
            ->rows(7);

        // $form->add('image', SingleImageDragField::class)
        //     ->label($this->trans('unicorn.field.image'))
        //     ->crop(true)
        //     ->width(800)
        //     ->height(600);
    }

    #[FormDefine]
    #[Fieldset('meta')]
    #[NS('item')]
    public function meta(Form $form): void
    {
        $form->add('public', SwitcherField::class)
            ->label('可使用公開網址')
            ->circle(true)
            ->color('primary')
            ->defaultValue('0');

        $form->add('publish_up', CalendarField::class)
            ->label('開始發佈');

        $form->add('publish_down', CalendarField::class)
            ->label('結束發佈');

        $form->add('extends', ListField::class)
            ->label('版面')
            ->defaultValue($this->formkit->getDefaultExtends() ?: null)
            ->registerOptions(
                $this->formkit->getExtendsOptions($this->lang)
            );

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
