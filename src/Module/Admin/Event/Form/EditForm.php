<?php

declare(strict_types=1);

namespace App\Module\Admin\Event\Form;

use Lyrasoft\Luna\Field\CategoryListField;
use Lyrasoft\Luna\Field\UserModalField;
use Unicorn\Field\CalendarField;
use Unicorn\Field\MultiUploaderField;
use Unicorn\Field\SingleImageDragField;
use Unicorn\Field\SwitcherField;
use Unicorn\Field\TinymceEditorField;
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
        $form->add('subtitle', TextField::class)
            ->label('副標題');

        $form->add('intro', TextareaField::class)
            ->label('簡介')
            ->rows(7);

        $form->add('description', TinymceEditorField::class)
            ->label($this->trans('unicorn.field.description'))
            ->editorOptions(
                [
                    'height' => 500
                ]
            );

        $form->add('images', MultiUploaderField::class)
            ->label('圖片集');
    }

    #[FormDefine]
    #[Fieldset('meta')]
    #[NS('item')]
    public function meta(Form $form): void
    {
        $form->add('category_id', CategoryListField::class)
            ->label('分類')
            ->option($this->trans('unicorn.select.placeholder'), '')
            ->categoryType('event');

        $form->add('cover', SingleImageDragField::class)
            ->label('封面')
            ->crop(true)
            ->width(1200)
            ->height(800)
            ->showSizeNotice(true);

        $form->add('state', SwitcherField::class)
            ->label($this->trans('unicorn.field.published'))
            ->circle(true)
            ->color('success')
            ->defaultValue('1');

        $form->add('publish_up', CalendarField::class)
            ->label('發佈時間')
            ->help('活動上架的時間');

        $form->add('start_date', CalendarField::class)
            ->label('活動開始時間')
            ->help('活動本身的開始時間');

        $form->add('end_date', CalendarField::class)
            ->label('活動結束時間')
            ->help('活動本身的結束時間');

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
