<?php

declare(strict_types=1);

namespace App\Module\Admin\Comment;

use App\Entity\Comment;
use App\Module\Admin\Comment\Form\EditForm;
use App\Repository\CommentRepository;
use Unicorn\View\FormAwareViewModelTrait;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;

use function Windwalker\unwrap_enum;

/**
 * The CommentEditView class.
 */
#[ViewModel(
    layout: 'comment-edit',
    js: 'comment-edit.js'
)]
class CommentEditView implements ViewModelInterface
{
    use TranslatorTrait;
    use ORMAwareViewModelTrait;
    use FormAwareViewModelTrait;

    public function __construct(
        #[Autowire] protected CommentRepository $repository,
    ) {
    }

    /**
     * Prepare
     *
     * @param  AppContext  $app
     * @param  View        $view
     *
     * @return  mixed
     */
    public function prepare(AppContext $app, View $view): mixed
    {
        $id = $app->input('id');
        $type = $app->input('type');

        $view['type'] = $type;

        /** @var ?Comment $item */
        $item = $this->repository->mustGetItem($id);

        // Bind item for injection
        $view[Comment::class] = $item;

        $form = $this->createForm(EditForm::class, type: $type)
            ->fill(
                [
                    'item' => $this->repository->getState()->getAndForget('edit.data')
                        ?: $this->orm->extractEntity($item)
                ]
            )
            ->fillTo('item', compact('type'));

        return compact('form', 'id', 'item', 'type');
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
                'feedback.comment.edit.title',
                title: $title
            )
        );
    }
}
