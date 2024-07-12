<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventOrderItemView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Data\EventOrderHistory;
use App\Data\EventOrderTotal;
use App\Entity\Event;
use App\Entity\EventOrder;
use App\Entity\EventStage;
use App\Module\Front\EventOrder\EventOrderItemView;
use App\Service\PriceFormatter;
use Lyrasoft\Luna\Entity\Category;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Form\Form;
use Windwalker\ORM\ORM;

/**
 * @var $form       Form
 * @var $item       EventOrder
 * @var $event      Event
 * @var $eventStage EventStage
 * @var $total      EventOrderTotal
 * @var $history    EventOrderHistory
 */

$priceFormatter = $app->retrieve(PriceFormatter::class);

$screenshots = $item->getScreenshots();

$event = $vm->tryEntity(Event::class, $screenshots['event']);
$stage = $vm->tryEntity(EventStage::class, $screenshots['stage']);


$orm = $app->retrieve(ORM::class);
$category = $orm->findOne(Category::class, $event->getId());
?>

@extends('global.body')

@section('content')
    <div class="container my-4">

        <div id="l-my-event" class="mx-auto" style="max-width: 800px">
            <div class="mb-4">
                {{-- Event Info --}}
                <x-components.event-info :event="$event" :stage="$stage" :category="$category" />
            </div>

            <x-tabs id="l-event-tabs" keepactive="#l-my-event" variant="pills">
                <x-tab title="報名 / 報到資訊">
                    @foreach ($attends as $attend)
                        <x-components.event-attend-info :attend="$attend" class="mb-4" />
                    @endforeach
                </x-tab>

                <x-tab title="活動詳情">
                    <div class="d-flex flex-column gap-4">
                        <div class="alert alert-primary">
                            此為報名時的快照，僅保留當下報名時的活動資訊
                        </div>

                        <div class="text-center">
                            <img src="{{ $stage->getCover() }}" class="img-fluid" style="max-width: 800px" alt="cover">
                        </div>

                        <header class="l-event-stage-header">
                            <h2>{{ $event->getTitle() }} | {{ $stage->getTitle() }}</h2>
                        </header>

                        <aside>
                            <div class="d-flex align-items-center gap-3 mb-2">
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
                                            class="link-secondary">
                                            <i class="far fa-folder"></i>
                                            {{ $category->getTitle() }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </aside>

                        <div>
                            {!! $event->getDescription() !!}
                        </div>

                        <div>
                            {!! $stage->getDescription() !!}
                        </div>
                    </div>



                </x-tab>
            </x-tabs>
        </div>
    </div>
@stop
