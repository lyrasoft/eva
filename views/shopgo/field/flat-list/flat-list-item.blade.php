<?php

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

declare(strict_types=1);

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var $field \Unicorn\Field\FlatListField
 * @var $node  \Lyrasoft\Luna\Tree\NodeInterface
 */

$option = $field->createItemOption($node->getValue());
$option->addClass('form-check-input');

$listId = 'c-flat-list-item--' . $option['value'];
?>

<li id="{{ $listId }}" class="nav-item c-flat-list-option"
    data-role="list-wrapper">
    <div class="form-check c-flat-list-option__wrapper">
        {!! $option !!}
        <label for="{{ $option['id'] }}">
            {{ $option['data-label'] }}
        </label>

        @if (!$node->isLeaf())
            @include('shopgo.field.flat-list.toggle-all')
        @endif
    </div>

    @if (!$node->isLeaf())
        <ul class="nav flex-column ps-3 mb-2">
            @foreach ($node->getChildren() as $child)
                @include('shopgo.field.flat-list.flat-list-item', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
