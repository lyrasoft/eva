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

use Unicorn\Legacy\Html\MenuHelper;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$menu = $app->service(MenuHelper::class);

$url = $nav->self();

?>

<div class="card">
    <div class="card-body p-2">
        <nav class="nav nav-pills">
            <a class="nav-item nav-link bg-light me-2"
                href="{{ $nav->to('event_list') }}">
                <i class="far fa-chevron-left"></i>
                回到活動列表
            </a>

            <a class="nav-item nav-link {{ $menu->active('event_edit') }}"
                href="{{ $nav->to('event_edit')->id($eventId) }}">
                活動編輯
            </a>
            <a class="nav-item nav-link {{ $menu->active(['event_stage_list', 'event_stage_edit']) }} {{ $eventId ? '' : 'disabled' }}"
                @attr('href', $eventId ? $nav->to('event_stage_list')->var('eventId', $eventId) : null)
            >
                活動梯次
            </a>
            <a class="nav-item nav-link"
                @attr('href', '#')
            >
                報名者資訊
            </a>
        </nav>
    </div>
</div>
