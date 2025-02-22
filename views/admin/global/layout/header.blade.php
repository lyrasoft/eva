<?php

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $view      ViewModel       The view modal object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

declare(strict_types=1);

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$user = $app->service(\Lyrasoft\Luna\User\UserService::class)->getUser();
?>

@section('header')
    <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                    {{-- Menu Start --}}
                    <ul class="navbar-nav">
                        {{-- Dashboard --}}
                        <li class="nav-item">
                            <a class="nav-link" href="./">
                                <i class="far fa-dashboard"></i>
                                <span class="nav-link-title">
                                    Dashboard
                                </span>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#"
                                data-bs-toggle="dropdown">
                                <i class="far fa-dashboard"></i>
                                <span class="nav-link-title">
                                    Foo
                                </span>
                            </a>
                            <div class="dropdown-menu">
                                <a href="#" class="dropdown-item">
                                    Hello
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="navbar-nav flex-row order-md-last gap-2">
                <div class="d-none d-lg-flex gap-2">
                    {{-- Fullscreen --}}
                    <div class="nav-item">
                        <button type="button"
                            uni-ripple
                            class="nav-link"
                            data-bs-toggle="fullscreen"
                            data-bs-placement="bottom"
                            title="Fullscreen"
                        >
                            <i class="fa-regular fa-expand"></i>
                        </button>
                    </div>

                    {{-- Preview Button --}}
                    <div class="nav-item">
                        <a href="{{ $nav->to('home') }}"
                            uni-ripple
                            class="nav-link"
                            data-bs-toggle="tooltip"
                            data-bs-placement="bottom"
                            title="See Frontend"
                        >
                            <i class="fa-regular fa-eye"></i>
                        </a>
                    </div>
                </div>
                @if ($user->isLogin())
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                            <img class="avatar avatar-sm" src="{{ $user->getAvatar() }}" alt="Avatar">
                            <div class="d-none d-xl-block ps-2">
                                <div>{{ $user->getName() }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ $nav->to('user_edit')->id($user->getId()) }}" class="dropdown-item gap-2">
                                <i class="far fa-user"></i>
                                <span>My Profile</span>
                            </a>

                            <a href="javascript:void(0)" class="dropdown-item link-danger gap-2"
                                onclick="u.form().post('{{ $nav->to('logout') }}')">
                                <i class="far fa-power-off"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </header>
@show
