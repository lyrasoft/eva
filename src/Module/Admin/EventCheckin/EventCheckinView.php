<?php

declare(strict_types=1);

namespace App\Module\Admin\EventCheckIn;

use App\Entity\EventAttend;
use App\Enum\AttendState;
use Unicorn\View\ORMAwareViewModelTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;

#[ViewModel(
    layout: 'event-checkin',
    js: 'event-checkin.js'
)]
class EventCheckinView implements ViewModelInterface
{
    use ORMAwareViewModelTrait;

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
        $no = $app->input('attendNo');

        $attend = $this->orm->mustFindOne(EventAttend::class, compact('no'));

        if ($attend->getState() !== AttendState::CHECKED_IN) {
            throw new RouteNotFoundException();
        }

        return compact('attend');
    }
}
