<?php

declare(strict_types=1);

namespace App\Module\Admin\Redirect\Form;

use Unicorn\Enum\BasicState;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Attributes\FormDefine;
use Windwalker\Form\Attributes\NS;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\SearchField;
use Windwalker\Form\Field\TextField;
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
        $form->add('redirect.state', ListField::class)
            ->label($this->trans('unicorn.field.state'))
            ->option($this->trans('unicorn.select.placeholder'), '')
            ->registerFromEnums(BasicState::class, $this->lang)
            ->onchange('this.form.submit()');

        $form->add('redirect.status', TextField::class)
            ->label($this->trans('firewall.redirect.field.status'))
            ->option('301 Moved Permanently', '301')
            ->option('302 Found', '302')
            ->option('303 See Other', '303')
            ->option('307 Temporary Redirect', '307')
            ->onchange('this.form.submit()');

        $form->add('redirect.params ->> regex', ListField::class)
            ->label($this->trans('firewall.redirect.field.regex'))
            ->option($this->trans('unicorn.select.placeholder'), '')
            ->option($this->trans('unicorn.core.yes'), '1')
            ->option($this->trans('unicorn.core.no'), '0')
            ->onchange('this.form.submit()');

        $form->add('redirect.params ->> not_found_only', ListField::class)
            ->label($this->trans('firewall.redirect.field.404.only'))
            ->option($this->trans('unicorn.select.placeholder'), '')
            ->option($this->trans('unicorn.core.yes'), '1')
            ->option($this->trans('unicorn.core.no'), '0')
            ->onchange('this.form.submit()');

        $form->add('redirect.params ->> handle_lang', ListField::class)
            ->label($this->trans('firewall.redirect.field.handle.lang'))
            ->option($this->trans('unicorn.select.placeholder'), '')
            ->option($this->trans('unicorn.core.yes'), '1')
            ->option($this->trans('unicorn.core.no'), '0')
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
