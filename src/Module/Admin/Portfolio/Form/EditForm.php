<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Admin\Portfolio\Form;

use Lyrasoft\Luna\Field\CategoryListField;
use Lyrasoft\Luna\Field\UserModalField;
use Unicorn\Field\CalendarField;
use Unicorn\Field\MultiUploaderField;
use Unicorn\Field\SingleImageDragField;
use Unicorn\Field\SwitcherField;
use Unicorn\Field\TinymceEditorField;
use Windwalker\Form\Field\HiddenField;
use Windwalker\Form\Field\NumberField;
use Windwalker\Form\Field\TextareaField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\Field\UrlField;
use Windwalker\Form\FieldDefinitionInterface;
use Windwalker\Form\Form;

/**
 * The EditForm class.
 */
class EditForm implements FieldDefinitionInterface
{
    /**
     * Define the form fields.
     *
     * @param  Form  $form  The Windwalker form object.
     *
     * @return  void
     */
    public function define(Form $form): void
    {
        $form->add('title', TextField::class)
            ->label('Title')
            ->addFilter('trim')
            ->required(true);

        $form->add('alias', TextField::class)
            ->label('Alias')
            ->addFilter('trim');

        $form->fieldset(
            'basic',
            function (Form $form) {
                $form->add('subtitle', TextField::class)
                    ->label('副標題');

                $form->add('url', TextField::class)
                    ->label('網址');

                $form->add('cover', SingleImageDragField::class)
                    ->label('封面圖');

                $form->add('images', MultiUploaderField::class)
                    ->label('Images')
                    ->accept(
                        implode(',', [
                            'image/png',
                            'image/jpeg',
                            'image/gif',
                            'image/webp',
                            'image/svg',
                            'image/svg+xml',
                        ])
                    )
                    ->configureForm(
                        function (Form $form) {
                            $form->add('title', TextField::class)
                                ->label('標題');
                            $form->add('description', TextareaField::class)
                                ->label('說明');
                            $form->add('link', UrlField::class)
                                ->label('連結');
                        }
                    );
            }
        );

        $form->fieldset(
            'meta',
            function (Form $form) {
                $form->add('category_id', CategoryListField::class)
                    ->label('Category ID')
                    ->categoryType('portfolio');

                $form->add('state', SwitcherField::class)
                    ->label('Published')
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

        $form->fieldset(
            'content',
            function (Form $form) {
                $form->add('description', TinymceEditorField::class)
                    ->label('Description')
                    ->editorOptions(
                        [
                            'height' => 500,
                        ]
                    );
            }
        );

        $form->add('id', HiddenField::class);
    }
}
