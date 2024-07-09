<?php

declare(strict_types=1);

namespace App\Module\Admin\EventAttend\Form;

use App\Field\EventModalField;
use App\Field\EventStageModalField;
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

    public function __construct(protected bool $inStage = false, protected ?int $eventId = null)
    {
    }

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
        $form->add('event_attend.state', ListField::class)
            ->label($this->trans('unicorn.field.state'))
            ->option($this->trans('unicorn.select.placeholder'), '')
            ->registerFromEnums(BasicState::class, $this->lang)
            ->onchange('this.form.submit()');

        if (!$this->inStage) {
            $form->add('event_attend.event_id', EventModalField::class)
                ->label('活動')
                ->onchange('this.form.submit()');

            $form->add('event_attend.stage_id', EventStageModalField::class)
                ->label('活動梯次')
                ->setEventId($this->eventId)
                ->disabled(!$this->eventId)
                ->tapIf(
                    !$this->eventId,
                    fn (EventStageModalField $field) => $field->placeholder('請先選擇活動')
                )
                ->onchange('this.form.submit()');
        }
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
