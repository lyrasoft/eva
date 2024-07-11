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

use App\Data\EventAttendingData;
use App\Data\EventAttendingPlan;
use App\Entity\EventPlan;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Edge\Component\ComponentAttributes;

use function Windwalker\tid;

/**
 * @var $plan       EventAttendingPlan
 * @var $data       EventAttendingData
 * @var $attributes ComponentAttributes
 */

$attributes->props(
    'data'
);

$i = 0;
?>

<div class="l-attendee-form">
    <h3 class="mb-3">報名者資料</h3>

    @foreach ($data->getAttendingPlans() as $plan)
        @if (!$plan->getQuantity())
            @continue
        @endif

        <div class="mb-4">
            <h4>方案: {{ $plan->getPlan()->getTitle() }}</h4>

            @foreach (range(1, $plan->getQuantity()) as $k)
                @php
                    $i++;
                    $uid = tid();
                @endphp
                <div>
                    <div class="card bg-light mb-3" data-uid="{{ $uid }}">
                        <div class="card-body">
                            <div class="card-title mb-3">
                                #{{ $i }}
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    {{-- Name --}}
                                    <div class="form-group mb-4">
                                        <label for="input-attend-{{ $uid }}-name"
                                            class="form-label">
                                            姓名 *
                                        </label>
                                        <input id="input-attend-{{ $uid }}-name" type="text"
                                            class="form-control"
                                            name="attends[{{ $uid }}][name]"
                                            required
                                        />
                                    </div>

                                    {{-- Email --}}
                                    <div class="form-group mb-4">
                                        <label for="input-attend-{{ $uid }}-email"
                                            class="form-label">
                                            Email *
                                        </label>
                                        <input id="input-attend-{{ $uid }}-email" type="email"
                                            class="form-control"
                                            name="attends[{{ $uid }}][email]"
                                            required
                                        />
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    {{-- Nick --}}
                                    <div class="form-group mb-4">
                                        <label for="input-attend-{{ $uid }}-nick"
                                            class="form-label">
                                            暱稱
                                        </label>
                                        <input id="input-attend-{{ $uid }}-nick" type="text"
                                            class="form-control"
                                            name="attends[{{ $uid }}][nick]"
                                        />
                                    </div>

                                    {{-- Mobile --}}
                                    <div class="form-group mb-4">
                                        <label for="input-attend-{{ $uid }}-mobile"
                                            class="form-label">
                                            手機 *
                                        </label>
                                        <input id="input-attend-{{ $uid }}-mobile" type="tel"
                                            class="form-control"
                                            name="attends[{{ $uid }}][mobile]"
                                            pattern="09\d{8}"
                                            required
                                        />
                                        <div class="text-muted small mt-1">
                                            格式: 09 開頭共 10 碼數字，不加 -
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-none">
                        <input type="hidden" name="attends[{{ $uid }}][plan_id]" value="{{ $plan->getPlan()->getId() }}" />
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

</div>
