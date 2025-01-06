<?php

declare(strict_types=1);

namespace App\Module\Admin\Comment;

use App\Entity\Comment;
use App\Module\Admin\Comment\Form\GridForm;
use App\Repository\CommentRepository;
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

use function Windwalker\unwrap_enum;

/**
 * The CommentListView class.
 */
#[ViewModel(
    layout: [
        'default' => 'comment-list',
        'modal' => 'comment-modal',
    ],
    js: 'comment-list.js'
)]
class CommentListView implements ViewModelInterface, FilterAwareViewModelInterface
{
    use TranslatorTrait;
    use FilterAwareViewModelTrait;
    use ORMAwareViewModelTrait;
    use FormAwareViewModelTrait;

    public function __construct(
        #[Autowire]
        protected CommentRepository $repository,
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

        $view['type'] = $type;

        // Prepare Items
        $page     = $state->rememberFromRequest('page');
        $limit    = $state->rememberFromRequest('limit') ?? 30;
        $filter   = (array) $state->rememberMergeRequest('filter');
        $search   = (array) $state->rememberMergeRequest('search');
        $ordering = $state->rememberFromRequest('list_ordering') ?? $this->getDefaultOrdering();

        $items = $this->repository->getListSelector($type)
            ->setFilters($filter)
            ->searchTextFor(
                $search['*'] ?? '',
                $this->getSearchFields()
            )
            ->ordering($ordering)
            ->page($page)
            ->limit($limit)
            ->setDefaultItemClass(Comment::class);

        $pagination = $items->getPagination();

        // Prepare Form
        $form = $this->createForm(GridForm::class, type: $type)
            ->fill(compact('search', 'filter'));

        $showFilters = $this->isFiltered($filter);

        return compact('items', 'pagination', 'form', 'showFilters', 'ordering', 'type');
    }

    /**
     * Get default ordering.
     *
     * @return  string
     */
    public function getDefaultOrdering(): string
    {
        return 'comment.id DESC';
    }

    /**
     * Get search fields.
     *
     * @return  string[]
     */
    public function getSearchFields(): array
    {
        return [
            'comment.id',
            'comment.title',
            'comment.content',
        ];
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame, string|\BackedEnum $type): void
    {
        $type = unwrap_enum($type);

        if ($this->lang->has('app.' . $type . '.title')) {
            $title = $this->trans('app.' . $type . '.title');
        } else {
            $title = $this->trans('luna.' . $type . '.title');
        }

        $htmlFrame->setTitle(
            $this->trans(
                'feedback.comment.list.title',
                title: $title
            )
        );
    }
}
