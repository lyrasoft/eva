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
 * @var $tree  \Lyrasoft\Luna\Tree\NodeInterface
 */

?>

<div id="{{ $field->getId('__inner') }}" class="c-flat-list" data-role="list-wrapper">
    <div class="mb-3">
        @include('shopgo.field.flat-list.toggle-all')
    </div>

    <input name="{{ $field->getInputName() }}" type="hidden" value="__EMPTY_ARRAY__" />

    <ul class="nav flex-column">
        @foreach ($tree->getChildren() as $node)
            @include('shopgo.field.flat-list.flat-list-item', ['node' => $node])
        @endforeach
    </ul>
</div>

<script>
    (function (fieldWrapper) {
      [].forEach.call(fieldWrapper.querySelectorAll('[data-task=children-toggle]'), function (button) {
        button.addEventListener('click', function () {
          var toggle = button.dataset.toggle || 'all';
          var wrapper = button.closest('[data-role=list-wrapper]');
          u.selectAll(wrapper.querySelectorAll('input'))
            .forEach(function (input) {
              input.checked = toggle === 'all';
            });
        });
      });
    })(document.querySelector('#{{ $field->getId('__inner') }}'));
</script>
