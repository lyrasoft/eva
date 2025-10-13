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
    <header class="navbar navbar-expand-md d-print-none position-sticky" style="top: 0; z-index: 5; min-height: var(--nx-header-height)">
        <div class="container-fluid">
            {{-- Sidebar Toggler --}}
            <div class="navbar-nav">
                <div class="nav-item">
                    <button class="nav-link" uni-ripple type="button" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Sidebar Toggler"
                        data-nx-toggle="sidebar">
                        <i class="far fa-bars"></i>
                    </button>
                </div>
            </div>

            {{-- Collapse Menu --}}
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                    {{-- Menu Start --}}
                    <ul class="navbar-nav">
                        {{-- Dashboard --}}
                        <li class="nav-item">
                            <a class="nav-link" uni-ripple href="{{ $nav->to('home') }}">
                                <i class="far fa-dashboard"></i>
                                <span class="nav-link-title">
                                    儀表板
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="navbar-nav ms-auto flex-row gap-2">
                <div class="d-none d-lg-flex gap-2">
                    {{-- Fullscreen --}}
                    <div class="nav-item"
                        data-bs-toggle="tooltip"
                        data-bs-placement="bottom"
                        title="Fullscreen"
                    >
                        <button type="button"
                            uni-ripple
                            class="nav-link"
                            data-nx-toggle="fullscreen"
                        >
                            <i class="fa-regular fa-expand"></i>
                        </button>
                    </div>

                    {{-- Preview Button --}}
                    <div class="nav-item">
                        <a href="{{ $uri->root() }}"
                            uni-ripple
                            class="nav-link"
                            data-bs-toggle="tooltip"
                            data-bs-placement="bottom"
                            title="See Frontend"
                            target="_blank"
                        >
                            <i class="fa-regular fa-eye"></i>
                        </a>
                    </div>
                </div>
                @if ($user->isLogin())
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex gap-1 py-0" data-bs-toggle="dropdown" aria-label="Open user menu">
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
                                onclick="u.logout('{{ $nav->to('logout') }}')">
                                <i class="far fa-power-off"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Toggler --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </header>
@show
