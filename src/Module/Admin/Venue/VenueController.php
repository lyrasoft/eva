<?php

declare(strict_types=1);

namespace App\Module\Admin\Venue;

use App\Module\Admin\Venue\Form\EditForm;
use App\Repository\VenueRepository;
use Unicorn\Controller\CrudController;
use Unicorn\Controller\GridController;
use Unicorn\Upload\FileUploadManager;
use Unicorn\Upload\FileUploadService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Router\Navigator;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\DI\Attributes\Service;
use Windwalker\ORM\Event\AfterSaveEvent;

#[Controller()]
class VenueController
{
    public function save(
        AppContext $app,
        CrudController $controller,
        Navigator $nav,
        #[Autowire] VenueRepository $repository,
        #[Service(FileUploadManager::class, 'image')]
        FileUploadService $fileUploadService
    ): mixed {
        $form = $app->make(EditForm::class);

        $controller->afterSave(
            function (AfterSaveEvent $event) use ($repository, $app, $fileUploadService) {
                $data = $event->getData();

                $result = $fileUploadService->handleFileIfUploaded(
                    $app->file('item')['image'] ?? null,
                    'images/venue/cover-' . md5((string) $data['id']) . '.jpg',
                );

                $data['image'] = $result?->getUri(true) ?: $data['image'];

                $repository->save($data);
            }
        );

        $uri = $app->call($controller->saveWithNamespace(...), compact('repository', 'form'));

        return match ($app->input('task')) {
            'save2close' => $nav->to('venue_list'),
            default => $uri,
        };
    }

    public function delete(
        AppContext $app,
        #[Autowire] VenueRepository $repository,
        CrudController $controller
    ): mixed {
        return $app->call($controller->delete(...), compact('repository'));
    }

    public function filter(
        AppContext $app,
        #[Autowire] VenueRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call($controller->filter(...), compact('repository'));
    }

    public function batch(
        AppContext $app,
        #[Autowire] VenueRepository $repository,
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
        #[Autowire] VenueRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call($controller->copy(...), compact('repository'));
    }
}
