<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Portfolio\Module\Admin\Portfolio\Form;

use Lyrasoft\Luna\Field\CategoryListField;
use Lyrasoft\Luna\Field\UserModalField;
use Unicorn\Field\CalendarField;
use Unicorn\Field\MultiUploaderField;
use Unicorn\Field\SingleImageDragField;
use Unicorn\Field\SwitcherField;
use Unicorn\Field\TinymceEditorField;
use Windwalker\Core\Language\TranslatorTrait;
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
        $form->add('title', TextField::class)
            ->label($this->trans('unicorn.field.title'))
            ->addFilter('trim')
            ->required(true);

        $form->add('alias', TextField::class)
            ->label($this->trans('unicorn.field.alias'))
            ->addFilter('trim');

        $form->fieldset(
            'basic',
            function (Form $form) {
                $form->add('subtitle', TextField::class)
                    ->label($this->trans('portfolio.field.subtitle'));

                $form->add('url', TextField::class)
                    ->label($this->trans('portfolio.field.url'));

                $form->add('cover', SingleImageDragField::class)
                    ->label($this->trans('portfolio.field.cover'));

                $form->add('images', MultiUploaderField::class)
                    ->label($this->trans('portfolio.field.images'))
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
                                ->label($this->trans('unicorn.field.title'));
                            $form->add('description', TextareaField::class)
                                ->label($this->trans('unicorn.field.description'));
                            $form->add('link', UrlField::class)
                                ->label($this->trans('portfolio.field.link'));
                        }
                    );
            }
        );

        $form->fieldset(
            'meta',
            function (Form $form) {
                $form->add('category_id', CategoryListField::class)
                    ->label($this->trans('portfolio.field.category'))
                    ->categoryType('portfolio')
                    ->option($this->trans('unicorn.select.placeholder'));

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
                    ->label($this->trans('unicorn.field.created.by'))
                    ->disabled(true);

                $form->add('modified_by', UserModalField::class)
                    ->label($this->trans('unicorn.field.modified.by'))
                    ->disabled(true);
            }
        );

        $form->fieldset(
            'content',
            function (Form $form) {
                $form->add('description', TinymceEditorField::class)
                    ->label($this->trans('unicorn.field.description'))
                    ->editorOptions(
                        [
                            'height' => 500,
                        ]
                    );
            }
        );

        $form->fieldset(
            'seo',
            function (Form $form) {
                $form->add('meta/title', TextField::class)
                    ->label($this->trans('portfolio.field.seo.title'));

                $form->add('meta/description', TextareaField::class)
                    ->label($this->trans('portfolio.field.seo.description'))
                    ->rows(7);

                $form->add('meta/keyword', TextField::class)
                    ->label($this->trans('portfolio.field.seo.keyword'));
            }
        );

        $form->add('id', HiddenField::class);
    }
}
