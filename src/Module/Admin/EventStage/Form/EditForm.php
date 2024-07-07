<?php

declare(strict_types=1);

namespace App\Module\Admin\EventStage\Form;

use App\Field\VenueListField;
use Lyrasoft\Luna\Field\UserModalField;
use Unicorn\Field\CalendarField;
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
use Windwalker\Form\Field\UrlField;
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
        $form->add('venue_id', VenueListField::class)
            ->label('場地')
            ->option($this->trans('unicorn.select.placeholder'), '');

        $form->add('quota', NumberField::class)
            ->label('人數限制');

        $form->add('less', NumberField::class)
            ->label('最低人數');

        $form->add('alternate', NumberField::class)
            ->label('可候補人數');

        $form->add('description', TinymceEditorField::class)
            ->label($this->trans('unicorn.field.description'))
            ->editorOptions(
                [
                    'height' => 500
                ]
            );
    }


    #[FormDefine]
    #[Fieldset('meta')]
    #[NS('item')]
    public function meta(Form $form): void
    {
        $form->add('attend_url', UrlField::class)
            ->label('報名連結')
            ->help('用以取代內建報名機制');

        $form->add('state', SwitcherField::class)
            ->label($this->trans('unicorn.field.published'))
            ->circle(true)
            ->color('success')
            ->defaultValue('1');

        $form->add('start_date', CalendarField::class)
            ->label('Start Date');

        $form->add('end_date', CalendarField::class)
            ->label('End Date');

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

        $form->add('event_id', HiddenField::class);
    }
}
