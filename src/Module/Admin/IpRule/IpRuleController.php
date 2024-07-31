<?php

declare(strict_types=1);

namespace App\Module\Admin\IpRule;

use App\Module\Admin\IpRule\Form\EditForm;
use App\Repository\IpRuleRepository;
use App\Service\FirewallService;
use Unicorn\Controller\CrudController;
use Unicorn\Controller\GridController;
use Unicorn\Repository\Event\PrepareSaveEvent;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\Core\Router\Navigator;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\Event\BeforeSaveEvent;

#[Controller()]
class IpRuleController
{
    public function save(
        AppContext $app,
        CrudController $controller,
        Navigator $nav,
        #[Autowire] IpRuleRepository $repository,
    ): mixed {
        $form = $app->make(EditForm::class);

        $controller->prepareSave(
            function (PrepareSaveEvent $event) use ($app) {
                $data = &$event->getData();

                $data['type'] = $app->input('type');
            }
        );

        $controller->beforeSave(
            function (BeforeSaveEvent $event) use ($app) {
                $data = &$event->getData();

                $range = FirewallService::createRangeInstance($data['range']);

                if (!$range) {
                    throw new ValidateFailException(
                        '不允許的 IP 格式'
                    );
                }
            }
        );

        $uri = $app->call($controller->saveWithNamespace(...), compact('repository', 'form'));

        return match ($app->input('task')) {
            'save2close' => $nav->to('ip_rule_list'),
            default => $uri,
        };
    }

    public function delete(
        AppContext $app,
        #[Autowire] IpRuleRepository $repository,
        CrudController $controller
    ): mixed {
        return $app->call($controller->delete(...), compact('repository'));
    }

    public function filter(
        AppContext $app,
        #[Autowire] IpRuleRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call($controller->filter(...), compact('repository'));
    }

    public function batch(
        AppContext $app,
        #[Autowire] IpRuleRepository $repository,
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
        #[Autowire] IpRuleRepository $repository,
        GridController $controller
    ): mixed {
        return $app->call($controller->copy(...), compact('repository'));
    }
}
