<?php

declare(strict_types=1);

namespace App\Module\Admin\IpRule;

use App\Entity\IpRule;
use App\Module\Admin\IpRule\Form\EditForm;
use App\Repository\IpRuleRepository;
use Unicorn\View\FormAwareViewModelTrait;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;

/**
 * The IpRuleEditView class.
 */
#[ViewModel(
    layout: 'ip-rule-edit',
    js: 'ip-rule-edit.js'
)]
class IpRuleEditView implements ViewModelInterface
{
    use TranslatorTrait;
    use ORMAwareViewModelTrait;
    use FormAwareViewModelTrait;

    public function __construct(
        #[Autowire] protected IpRuleRepository $repository,
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

        /** @var IpRule $item */
        $item = $this->repository->getItem($id);

        if ($item && $item->getType() !== $type) {
            throw new RouteNotFoundException();
        }

        // Bind item for injection
        $view[IpRule::class] = $item;

        $form = $this->createForm(EditForm::class)
            ->fill(
                [
                    'item' => $this->repository->getState()->getAndForget('edit.data')
                        ?: $this->orm->extractEntity($item)
                ]
            );

        return compact('form', 'id', 'item');
    }

    #[ViewMetadata]
    protected function prepareMetadata(HtmlFrame $htmlFrame): void
    {
        $htmlFrame->setTitle(
            $this->trans('unicorn.title.edit', title: $this->trans('firewall.ip.rule.title'))
        );
    }
}
