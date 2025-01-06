<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        CommentListView The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\Comment;
use Unicorn\Image\ImagePlaceholder;
use Unicorn\Workflow\BasicStateWorkflow;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use App\Module\Admin\Comment\CommentListView;

use function Windwalker\str;

/**
 * @var $item Comment
 */

$workflow = $app->service(BasicStateWorkflow::class);

$imgPlaceholder = $app->retrieve(ImagePlaceholder::class);
?>

@extends('admin.global.body-list')

@section('toolbar-buttons')
    @include('list-toolbar')
@stop

@section('content')
    <form id="admin-form" action="" x-data="{ grid: $store.grid }"
        x-ref="gridForm"
        data-ordering="{{ $ordering }}"
        method="post">

        <x-filter-bar :form="$form" :open="$showFilters"></x-filter-bar>

        {{-- RESPONSIVE TABLE DESC --}}
        <div class="d-block d-lg-none mb-3">
            @lang('unicorn.grid.responsive.table.desc')
        </div>

        <div class="grid-table table-responsive-lg">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    {{-- Toggle --}}
                    <th style="width: 1%">
                        <x-toggle-all></x-toggle-all>
                    </th>

                    {{-- State --}}
                    <th style="width: 5%" class="text-nowrap">
                        <x-sort field="comment.state">
                            @lang('unicorn.field.state')
                        </x-sort>
                    </th>

                    <th style="width: 1%" class="text-nowrap">
                        @lang('feedback.text.edit')
                    </th>

                    {{-- User --}}
                    <th class="text-nowrap">
                        @lang('feedback.field.user')
                    </th>

                    {{-- Content --}}
                    <th class="text-nowrap">
                        <x-sort field="comment.content">
                            @lang('feedback.field.content')
                        </x-sort>
                    </th>

                    {{-- Reply --}}
                    <th class="text-nowrap">
                        @lang('feedback.comment.field.reply')
                    </th>

                    {{-- Delete --}}
                    <th style="width: 1%" class="text-nowrap">
                        @lang('unicorn.field.delete')
                    </th>

                    {{-- ID --}}
                    <th style="width: 1%" class="text-nowrap text-end">
                        <x-sort field="comment.id">
                            @lang('unicorn.field.id')
                        </x-sort>
                    </th>
                </tr>
                </thead>

                <tbody>
                @forelse($items as $i => $item)
                    <tr>
                        {{-- Checkbox --}}
                        <td>
                            <x-row-checkbox :row="$i" :id="$item->getId()"></x-row-checkbox>
                        </td>

                        {{-- State --}}
                        <td>
                            <x-state-dropdown color-on="text"
                                button-style="width: 100%"
                                use-states
                                :workflow="$workflow"
                                :id="$item->getId()"
                                :value="$item->state"
                            ></x-state-dropdown>
                        </td>

                        {{-- Edit --}}
                        <td class="text-nowrap">
                            <a href="{{ $nav->to('comment_edit')->id($item->getId()) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fa fa-edit"></i>
                                @lang('feedback.text.edit')
                            </a>
                        </td>

                        {{-- User --}}
                        <td class="text-nowrap">
                            @php
                                $avatar = $item->user?->avatar ?: $item->getAvatar() ?: $imgPlaceholder->avatar();
                                $nickName = $item->user?->name ?: $item->getNickname() ?: $lang('feedback.text.anonymous');
                            @endphp

                            <div class="d-flex align-items-center gap-2">
                                <div>
                                    <img src="{{ $avatar }}" class="rounded-circle" alt="Avatar"
                                        style="height: 28px">
                                </div>
                                <div class="small">
                                    <a @attr('href', $item->getUserId() ? $nav->to('user_edit')->id($item->getUserId()) : null)
                                        target="_blank"
                                        class="link-secondary">
                                        {{ $nickName }}
                                    </a>
                                </div>
                            </div>
                        </td>

                        {{-- Content --}}
                        <td>
                            <div>
                                <h5>
                                    {{ $item->getTitle() }}
                                </h5>
                            </div>
                            <div class="text-muted">
                                {{ str($item->getContent())->stripHtmlTags()->truncate(100, '...') }}
                            </div>
                        </td>

                        {{-- Reply --}}
                        <td>
                            {{ str($item->getReply() ?? $item->last_reply?->content ?? '')->stripHtmlTags()->truncate(100, '...') }}
                        </td>

                        {{-- Delete --}}
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                @click="grid.deleteItem('{{ $item->getId() }}')"
                                data-dos
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>

                        {{-- ID --}}
                        <td class="text-end">
                            {{ $item->getId() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="30">
                            <div class="c-grid-no-items text-center" style="padding: 125px 0;">
                                <h3 class="text-secondary">@lang('unicorn.grid.no.items')</h3>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div>
                <x-pagination :pagination="$pagination">
                    <x-slot name="end">
                        <x-pagination-stats :pagination="$pagination" class="ms-0 ms-md-auto" />
                    </x-slot>
                </x-pagination>
            </div>
        </div>

        <div class="d-none">
            <input name="_method" type="hidden" value="PUT" />
            <x-csrf></x-csrf>
        </div>

        <x-batch-modal :form="$form" namespace="batch"></x-batch-modal>
    </form>

@stop
