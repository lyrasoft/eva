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

use App\Entity\EventAttend;
use chillerlan\QRCode\QRCode;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Edge\Component\ComponentAttributes;

/**
 * @var $attend     EventAttend
 * @var $attributes ComponentAttributes
 */

$attributes->props(
    'attend'
);

$link = $nav->to('admin::event_checkin')->var('attendNo', $attend->getNo())->full();

$qrcode = (new QRCode())->render((string) $link);
?>

<div {!! $attributes !!}>
    <div class="card bg-light">
        <div class="card-body">
            <div class="card-title mb-3">
                報名編號: <strong>#{{ $attend->getNo() }}</strong>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <table class="table table-borderless"
                        style="--bs-table-bg: transparent">
                        <tbody>
                        <tr>
                            <td style="width: 100px">姓名</td>
                            <td>
                                {{ $attend->getName() }}
                                @if ($attend->getNick())
                                    ({{ $attend->getNick() }})
                                @endif
                            </td>

                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $attend->getEmail() }}</td>
                        </tr>
                        <tr>
                            <th>手機</th>
                            <td>{{ $attend->getMobile() }}</td>
                        </tr>
                        </tbody>
                    </table>

                    @debug
                    <a href="{{ $link }}" class="btn btn-primary"
                        target="_blank">
                        測試報到
                    </a>
                    @enddebug
                </div>

                <div class="col-lg-5">
                    <img class="img-fluid" src="{{ $qrcode }}" alt="QRCode">
                </div>
            </div>
        </div>
    </div>
</div>
