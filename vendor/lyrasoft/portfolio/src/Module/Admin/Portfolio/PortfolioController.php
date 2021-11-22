<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Portfolio\Module\Admin\Portfolio;

use Lyrasoft\Portfolio\Module\Admin\Portfolio\Form\EditForm;
use Lyrasoft\Portfolio\Repository\PortfolioRepository;
use Unicorn\Controller\CrudController;
use Unicorn\Controller\GridController;
use Unicorn\Upload\FileUploadService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Router\Navigator;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\Event\AfterSaveEvent;

/**
 * The PortfolioController class.
 */
#[Controller()]
class PortfolioController
{
    public function save(
        AppContext $app,
        CrudController $controller,
        Navigator $nav,
        #[Autowire] PortfolioRepository $repository,
        FileUploadService $fileUploadService
    ): mixed {
        $form = $app->make(EditForm::class);

        $controller->afterSave(
            function (AfterSaveEvent $event) use ($repository, $app, $fileUploadService) {
                $data = $event->getData();

                $data['cover'] = $fileUploadService->handleFileIfUploaded(
                        $app->file('item')['cover'] ?? null
                    )?->getUri() ?? $data['cover'];

                $repository->save($data);
            }
        );

        $uri = $app->call([$controller, 'save'], compact('repository', 'form'));

        switch ($app->input('task')) {
            case 'save2close':
                return $nav->to(PortfolioListView::class);

            case 'save2new':
                return $nav->to(PortfolioEditView::class)->var('new', 1);

            case 'save2copy':
                $controller->rememberForClone($app, $repository);
                return $nav->self($nav::WITHOUT_VARS)->var('new', 1);

            default:
                return $uri;
        }
    }

    public function delete(
        AppContext $app,
        #[Autowire] PortfolioRepository $repository,
        CrudController $controller
    ): mixed {
        return $app->call([$controller, 'delete'], compact('repository'));
    }

    public function filter(
        AppContext $app,
        #[Autowire] PortfolioRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call([$controller, 'filter'], compact('repository'));
    }

    public function batch(
        AppContext $app,
        #[Autowire] PortfolioRepository $repository,
        GridController $controller
    ): mixed {
        $task = $app->input('task');
        $data = match ($task) {
            'publish' => ['state' => 1],
            'unpublish' => ['state' => 0],
            default => null
        };

        return $app->call([$controller, 'batch'], compact('repository', 'data'));
    }

    public function copy(
        AppContext $app,
        #[Autowire] PortfolioRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call([$controller, 'copy'], compact('repository'));
    }
}
