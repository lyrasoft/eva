<?php

declare(strict_types=1);

namespace App\Module\Admin\Comment;

use App\Module\Admin\Comment\Form\EditForm;
use App\Repository\CommentRepository;
use Unicorn\Controller\CrudController;
use Unicorn\Controller\GridController;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Router\Navigator;
use Windwalker\DI\Attributes\Autowire;

#[Controller()]
class CommentController
{
    public function save(
        AppContext $app,
        CrudController $controller,
        Navigator $nav,
        #[Autowire] CommentRepository $repository,
    ): mixed {
        $form = $app->make(EditForm::class);

        $uri = $app->call($controller->saveWithNamespace(...), compact('repository', 'form'));

        return match ($app->input('task')) {
            'save2close' => $nav->to('comment_list'),
            default => $uri,
        };
    }

    public function delete(
        AppContext $app,
        #[Autowire] CommentRepository $repository,
        CrudController $controller
    ): mixed {
        return $app->call($controller->delete(...), compact('repository'));
    }

    public function filter(
        AppContext $app,
        #[Autowire] CommentRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call($controller->filter(...), compact('repository'));
    }

    public function batch(
        AppContext $app,
        #[Autowire] CommentRepository $repository,
        GridController $controller
    ): mixed {
        $task = $app->input('task');
        $data = match ($task) {
            'publish' => ['state' => 1],
            'unpublish' => ['state' => 0],
            default => null
        };

        return $app->call($controller->batch(...), compact('repository', 'data'));
    }

    public function copy(
        AppContext $app,
        #[Autowire] CommentRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call($controller->copy(...), compact('repository'));
    }
}
