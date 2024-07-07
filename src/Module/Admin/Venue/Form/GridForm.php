<?php

declare(strict_types=1);

namespace App\Module\Admin\Venue\Form;

use Lyrasoft\Luna\Field\CategoryListField;
use Unicorn\Enum\BasicState;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Attributes\FormDefine;
use Windwalker\Form\Attributes\NS;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\SearchField;
use Windwalker\Form\Form;

class GridForm
{
    use TranslatorTrait;

    #[FormDefine]
    #[NS('search')]
    public function search(Form $form): void
    {
        $form->add('*', SearchField::class)
            ->label($this->trans('unicorn.grid.search.label'))
            ->placeholder($this->trans('unicorn.grid.search.label'))
            ->onchange('this.form.submit()');
    }

    #[FormDefine]
    #[NS('filter')]
    public function filter(Form $form): void
    {
        $form->add('venue.state', ListField::class)
            ->label($this->trans('unicorn.field.state'))
            ->option($this->trans('unicorn.select.placeholder'), '')
            ->registerFromEnums(BasicState::class, $this->lang)
            ->onchange('this.form.submit()');

        $form->add('venue.category_id', CategoryListField::class)
            ->label('分類')
            ->categoryType('venue')
            ->option($this->trans('unicorn.select.placeholder'), '')
            ->onchange('this.form.submit()');
    }

    #[FormDefine]
    #[NS('batch')]
    public function batch(Form $form): void
    {
        $form->add('state', ListField::class)
            ->label($this->trans('unicorn.field.state'))
            ->option($this->trans('unicorn.select.no.change'), '')
            ->registerFromEnums(BasicState::class, $this->lang);
    }
}
