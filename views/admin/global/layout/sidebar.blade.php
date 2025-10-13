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

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

?>
<aside class="l-sidebar navbar navbar-vertical navbar-expand" data-bs-theme="light">
    <div class="container-fluid">
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- LOGO --}}
        <div class="navbar-brand" style="height: var(--nx-header-height)">
            {{-- Dark Mode --}}
            <div class="d-light-none d-dark-block">
                <a href="{{ $nav->to('home') }}">
                    <img class="navbar-brand-image" src="{{ $asset->path('@vite/images/logo-cw-h.svg') }}"
                        alt="LOGO" style="width: auto; height: 30px;">
                    <img class="navbar-brand-icon" src="{{ $asset->path('@vite/images/icon.svg') }}"
                        alt="LOGO" style="width: auto; height: 40px;">
                </a>
            </div>

            {{-- Light Mode --}}
            <div class="d-dark-none d-light-block">
                <a href="{{ $nav->to('home') }}">
                    <img class="navbar-brand-image" src="{{ $asset->path('@vite/images/logo-cb-h.svg') }}"
                        alt="LOGO" style="width: auto; height: 30px;">
                    <img class="navbar-brand-icon" src="{{ $asset->path('@vite/images/icon.svg') }}"
                        alt="LOGO" style="width: auto; height: 40px;">
                </a>
            </div>
        </div>

        {{-- Sidemenu --}}
        <div class="collapse navbar-collapse l-sidebar-menu" id="sidebar-menu"
            data-bs-theme="dark">
            <ul class="navbar-nav nav-pills pt-lg-3">
                @include('global.layout.sidemenu')
            </ul>
        </div>
    </div>
</aside>
