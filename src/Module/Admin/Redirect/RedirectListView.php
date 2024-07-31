<?php

declare(strict_types=1);

namespace App\Module\Admin\Redirect;

use App\Entity\Redirect;
use App\Module\Admin\Redirect\Form\GridForm;
use App\Repository\RedirectRepository;
use Unicorn\View\FormAwareViewModelTrait;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\View\Contract\FilterAwareViewModelInterface;
use Windwalker\Core\View\Traits\FilterAwareViewModelTrait;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;

/**
 * The RedirectListView class.
 */
#[ViewModel(
    layout: [
        'default' => 'redirect-list',
        'modal' => 'redirect-modal',
    ],
    js: 'redirect-list.js'
)]
class RedirectListView implements ViewModelInterface, FilterAwareViewModelInterface
{
    use TranslatorTrait;
    use FilterAwareViewModelTrait;
    use ORMAwareViewModelTrait;
    use FormAwareViewModelTrait;

    public function __construct(
        #[Autowire]
        protected RedirectRepository $repository,
    ) {
    }

    /**
     * Prepare view data.
     *
     * @param  AppContext  $app   The request app context.
     * @param  View        $view  The view object.
     *
     * @return  array
     */
    public function prepare(AppContext $app, View $view): array
    {
        $state = $this->repository->getState();

        $type = $app->input('type');

        // Prepare Items
        $page     = $state->rememberFromRequest('page');
        $limit    = $state->rememberFromRequest('limit') ?? 30;
        $filter   = (array) $state->rememberFromRequest('filter');
        $search   = (array) $state->rememberFromRequest('search');
        $ordering = $state->rememberFromRequest('list_ordering') ?? $this->getDefaultOrdering();

        $items = $this->repository->getListSelector()
            ->setFilters($filter)
            ->searchTextFor(
                $search['*'] ?? '',
                $this->getSearchFields()
            )
            ->where('redirect.type', $type)
            ->ordering($ordering)
            ->page($page)
            ->limit($limit)
            ->setDefaultItemClass(Redirect::class);

        $pagination = $items->getPagination();

        // Prepare Form
        $form = $this->createForm(GridForm::class)
            ->fill(compact('search', 'filter'));

        $showFilters = $this->isFiltered($filter);

        return compact('items', 'pagination', 'form', 'showFilters', 'ordering');
    }

    public function reorderEnabled(string $ordering): bool
    {
        return $ordering === 'redirect.ordering ASC';
    }

    /**
     * Get default ordering.
     *
     * @return  string
     */
    public function getDefaultOrdering(): string
    {
        return 'redirect.ordering ASC';
    }

    /**
     * Get search fields.
     *
     * @return  string[]
     */
    public function getSearchFields(): array
    {
        return [
            'redirect.id',
            'redirect.title',
            'redirect.src',
            'redirect.dest',
            'redirect.status',
            'redirect.note',
        ];
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame): void
    {
        $htmlFrame->setTitle(
            $this->trans('unicorn.title.grid', title: $this->trans('firewall.redirect.title'))
        );
    }
}
