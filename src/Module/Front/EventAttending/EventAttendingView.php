<?php

declare(strict_types=1);

namespace App\Module\Front\EventAttending;

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;

#[ViewModel(
    layout: 'event-attending',
    js: 'event-attending.js'
)]
class EventAttendingView implements ViewModelInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Prepare View.
     *
     * @param  AppContext  $app   The web app context.
     * @param  View        $view  The view object.
     *
     * @return  mixed
     */
    public function prepare(AppContext $app, View $view): array
    {
        return [];
    }
}
