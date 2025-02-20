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

$menu = $app->service(\Unicorn\Legacy\Html\MenuHelper::class);

$root = $app->service(\Lyrasoft\Luna\Services\MenuService::class)
    ->loadMenuFromFile('sidemenu', WINDWALKER_RESOURCES . '/menu/admin/sidemenu.menu.php');
?>

@foreach ($root->getChildren() as $menuItem)
    @if ($menuItem->getLayout() === 'placeholder.header')
        <li class="nav-header px-3 small mb-2 text-secondary">
            {{ $menuItem->getTitle() }}
        </li>
    @else
        @if ($menuItem->hasChildren())
            <li class="nav-item dropdown">
                <a href="javascript: void(0);" class="nav-link dropdown-toggle {{ $menuItem->isActive(true) ? 'active show' : '' }}"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="false"
                    role="button"
                    aria-expanded="false">
                    <i class="{{ $menuItem->getIcon() }} fa-fw" style="font-size: 1rem"></i>
                    <span>{{ $menuItem->getTitle() }}</span>
                </a>
                <ul class="dropdown-menu {{ $menuItem->isActive(true) ? 'show' : '' }}" aria-expanded="false" style="">
                    @foreach ($menuItem->getChildren() as $childItem)
                        <a href="{{ $childItem->route($nav) }}"
                            class="dropdown-item {{ $childItem->isActive(true) ? 'active' : '' }}">
                            <span class="{{ $childItem->getIcon() }} fa-fw"></span>
                            <span>{{ $childItem->getTitle() }}</span>
                        </a>
                    @endforeach
                </ul>
            </li>
        @else
            <li class="nav-item {{ $childItem->isActive(true) ? 'active' : '' }}">
                <a href="{{ $menuItem->route($nav) }}"
                    class="nav-link {{ $menuItem->isActive(true) ? 'active' : '' }}">
                    <i class="{{ $menuItem->getIcon() }} fa-fw" style="font-size: 1rem"></i>
                    <span>{{ $menuItem->getTitle() }}</span>
                </a>
            </li>
        @endif
    @endif
@endforeach
