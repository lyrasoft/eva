<?php

declare(strict_types=1);

namespace App\view;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use App\Entity\Event;
use App\Entity\EventStage;
use Lyrasoft\Luna\Entity\Category;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

use Windwalker\Edge\Component\ComponentAttributes;

use function Windwalker\str;

/**
 * @var $event      Event
 * @var $stage      EventStage
 * @var $category   Category
 * @var $attributes ComponentAttributes
 */

$attributes->props(
    'event',
    'stage',
    'category'
);
?>
<x-card>
    <div class="row">
        <div class="col-lg-4">
            <img src="{{ $stage->getCover() ?: $event->getCover() }}" alt="cover"
                class="img-fluid rounded"
            >
        </div>
        <div class="col-lg-8 d-flex flex-column">
            <h3>
                <a href="{{ $stage->makeLink($nav) }}" target="_blank">
                    {{ $event->getTitle() }} | {{ $stage->getTitle() }}
                </a>
            </h3>
            <div class="mb-3">
                {!! str($stage->getDescription())->stripHtmlTags()->truncate(200, '...') !!}
            </div>

            <div class="d-flex align-items-center gap-3 mb-2 mt-auto">
                @if ($stage->getStartDate())
                    <div>
                        開始: {{ $chronos->toLocalFormat($stage->getStartDate(), 'Y/m/d H:i') }}
                    </div>
                @endif

                @if ($stage->getEndDate())
                    <div>
                        結束: {{ $chronos->toLocalFormat($stage->getEndDate(), 'Y/m/d H:i') }}
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3 mb-2">
                <div>
                    <i class="far fa-user"></i>
                    {{ $stage->getQuota() }}
                </div>

                @if ($category)
                    <div>
                        <a href="{{ $nav->to('event_stage_list')->var('path', $category->getPath()) }}"
                            class="link-secondary"
                            target="_blank"
                        >
                            <i class="far fa-folder"></i>
                            {{ $category->getTitle() }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-card>
