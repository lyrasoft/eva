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

use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$user = $app->service(UserService::class)->getUser();

?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ $uri->path() }}">
                <img src="{{ $asset->path('@vite/images/logo-cw-h.svg') }}"
                    alt="LOGO"
                    style="height: 27px;"
                />
            </a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <x-menu-root :menu="$menu" dropdown class="navbar-nav me-auto mb-2 mb-lg-0"></x-menu-root>

                <ul class="navbar-nav mb-2 mb-lg-0">
                    <x-locale-dropdown class="nav-item" />

                    <li class="nav-item">
                        <x-currency-dropdown button-class="nav-link" />
                    </li>

                    <li class="nav-item">
                        <x-cart-button class="nav-link" />
                    </li>

                    @if (!$user->isLogin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ $nav->to('login')->withReturn() }}">
                                <span class="fa fa-sign-in-alt"></span>
                                Login
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ $nav->to('logout') }}">
                                <span class="fa fa-sign-out-alt"></span>
                                Logout
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
        aria-labelledby="offcanvasNavbarLabel"
    >
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <x-menu-root vertical click :menu="$menu" class="navbar-nav me-auto mb-2 mb-lg-0"></x-menu-root>

            <ul class="navbar-nav mb-2 mb-lg-0">
                <x-locale-dropdown class="nav-item" />
            </ul>
        </div>
    </div>
</header>
