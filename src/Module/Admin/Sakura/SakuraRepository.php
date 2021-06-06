<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Admin\Sakura;

use App\Entity\Sakura;
use Unicorn\Attributes\Repository;
use Unicorn\Repository\Actions\ActionsFactory;
use Unicorn\Repository\ListRepositoryInterface;
use Unicorn\Repository\ListRepositoryTrait;
use Unicorn\Repository\ManageRepositoryInterface;
use Unicorn\Repository\ManageRepositoryTrait;
use Unicorn\Selector\ListSelector;
use Windwalker\ORM\SelectorQuery;

/**
 * The SakuraRepository class.
 */
#[Repository(entityClass: Sakura::class)]
class SakuraRepository implements ManageRepositoryInterface, ListRepositoryInterface
{
    use ManageRepositoryTrait;
    use ListRepositoryTrait;

    /**
     * Configure List Selector.
     *
     * @param  SelectorQuery  $query
     * @param  ListSelector   $selector
     *
     * @return  void
     */
    protected function configureSelector(SelectorQuery $query, ListSelector $selector): void
    {
        $query->from(Sakura::class);
    }

    /**
     * Configure Actions.
     * - SaveAction
     * - ReorderAction
     * - BatchAction
     *
     * @param  ActionsFactory  $actionsFactory
     *
     * @return  void
     */
    protected function configureActions(ActionsFactory $actionsFactory): void
    {
        // $actionsFactory->configure(ReorderAction::class, callable);
    }
}
