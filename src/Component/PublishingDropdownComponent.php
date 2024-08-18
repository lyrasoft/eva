<?php

declare(strict_types=1);

namespace App\Component;

use Closure;
use Unicorn\Component\StateDropdownComponent;
use Unicorn\Html\State\StateButton;
use Unicorn\Workflow\State;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Edge\Attribute\EdgeComponent;
use Windwalker\DI\Attributes\Inject;
use Windwalker\Edge\Component\AbstractComponent;
use Windwalker\Edge\Component\ComponentAttributes;
use Windwalker\Utilities\Attributes\Prop;

use function Windwalker\try_chronos;

#[EdgeComponent('publishing-dropdown')]
class PublishingDropdownComponent extends StateDropdownComponent
{
    #[Prop]
    public ?\DateTimeInterface $publishUp = null;

    #[Prop]
    public ?\DateTimeInterface $publishDown = null;

    #[Inject]
    protected ChronosService $chronosService;

    public function data(): array
    {
        $data = parent::data();

        $publishUp = $this->publishUp;
        $publishDown = $this->publishDown;

        if ($publishUp || $publishDown) {
            /** @var State $currentState */
            $currentState = $data['currentState'];
            $value = $currentState->getValue();
            // $default     = $this->getState($value) ?: $this->getState('_default');
            // $tz          = Factory::getUser()->getTimezone();
            $publishUp   = try_chronos($publishUp);
            $publishDown = try_chronos($publishDown);

            // Add tips and special titles
            // Create special titles for published items
            if ($value === '1') {
                // Create tip text, only we have publish up or down settings
                $tips = '已啟用項目';

                if ($publishUp && $publishUp->isFuture()) {
                    $tips = '等待發佈中';
                    $currentState->title('等待發佈');
                    $currentState->icon(' far fa-clock');
                    $data['textColor'] = 'text-warning';
                } elseif ($publishDown && $publishDown->isPast()) {
                    $tips = '已結束發佈';
                    $currentState->title('結束發佈');
                    $currentState->icon(' far fa-eye-slash');
                    $data['textColor'] = 'text-secondary';
                } else {
                    $tips = '發佈中';
                    $currentState->title('發佈中');
                    $currentState->icon(' far fa-play');
                    // $data['textColor'] = 'text-success';
                }

                if ($publishUp) {
                    $tips .= sprintf(
                        ' - 開始發佈日期: %s',
                        $this->chronosService->toLocalFormat($publishUp, 'Y/m/d H:i:s')
                    );
                }

                if ($publishDown) {
                    $tips .= sprintf(
                        ' - 結束發佈日期: %s',
                        $this->chronosService->toLocalFormat($publishDown, 'Y/m/d H:i:s')
                    );
                }

                $data['attributes']['data-bs-toggle'] = 'tooltip';
                $data['attributes']['title'] = $tips;
            }
        }

        return $data;
    }

    public function withAttributes(array $attributes, array|ComponentAttributes $binding = []): static
    {
        // if ($binding instanceof ComponentAttributes) {
        //     $binding = $binding->getAttributes();
        // }

        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        $this->attributes->setAttributes(
            [
                ...$this->attributes->getAttributes(),
                ...$attributes
            ]
        );

        return $this;
    }
}
