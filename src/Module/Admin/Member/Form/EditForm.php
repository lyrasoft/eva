<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Admin\Member\Form;

use Lyrasoft\Luna\Field\CategoryListField;
use Lyrasoft\Luna\Field\UserModalField;
use Unicorn\Field\CalendarField;
use Unicorn\Field\SwitcherField;
use Unicorn\Field\TinymceEditorField;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Field\EmailField;
use Windwalker\Form\Field\TextareaField;
use Unicorn\Field\SingleImageDragField;
use Windwalker\Form\Field\NumberField;
use Windwalker\Form\Field\HiddenField;
use Unicorn\Enum\BasicState;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\FieldDefinitionInterface;
use Windwalker\Form\Form;

/**
 * The EditForm class.
 */
class EditForm implements FieldDefinitionInterface
{
    use TranslatorTrait;

    /**
     * Define the form fields.
     *
     * @param  Form  $form  The Windwalker form object.
     *
     * @return  void
     */
    public function define(Form $form): void
    {
        $form->add('name', TextField::class)
            ->label('姓名')
            ->addFilter('trim');

        $form->add('alias', TextField::class)
            ->label('網址別名')
            ->addFilter('trim');

        $form->fieldset(
            'basic',
            function (Form $form) {
                $form->add('image', SingleImageDragField::class)
                    ->label('圖片')
                    ->crop(true)
                    ->width(400)
                    ->height(400);

                $form->add('intro', TextareaField::class)
                    ->label('簡介')
                    ->rows(7);

                $form->add('description', TinymceEditorField::class)
                    ->label('介紹')
                    ->editorOptions(
                        [
                            'height' => 550
                        ]
                    );
            }
        );

        $form->fieldset(
            'detail',
            function (Form $form) {
                $form->add('category_id', CategoryListField::class)
                    ->label('團隊分類')
                    ->categoryType('member')
                    ->option($this->trans('unicorn.select.placeholder'));

                $form->add('job_title', TextField::class)
                    ->label('職稱');

                $form->add('email', EmailField::class)
                    ->label('Email');

                $form->add('phone', TextField::class)
                    ->label('電話');
            }
        );

        $form->fieldset(
            'meta',
            function (Form $form) {
                $form->add('state', SwitcherField::class)
                    ->label('啟用')
                    ->circle(true)
                    ->color('success')
                    ->defaultValue('1');

                $form->add('created', CalendarField::class)
                    ->label('建立時間')
                    ->disabled(true);

                $form->add('modified', CalendarField::class)
                    ->label('編輯時間')
                    ->disabled(true);

                $form->add('created_by', UserModalField::class)
                    ->label('建立者')
                    ->disabled(true);

                $form->add('modified_by', UserModalField::class)
                    ->label('編輯者')
                    ->disabled(true);
            }
        );

        $form->add('id', HiddenField::class);
    }
}
