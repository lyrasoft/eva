<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Enum\AttendState;
use Unicorn\Attributes\StateMachine;
use Unicorn\Workflow\WorkflowController;
use Unicorn\Workflow\WorkflowInterface;
use Unicorn\Workflow\WorkflowTrait;

#[StateMachine(
    field: 'state',
    enum: \App\Enum\AttendState::class,
    // Set to FALSE to allow free transition.
    strict: true
)]
class EventAttendStateWorkflow implements WorkflowInterface
{
    use WorkflowTrait;

    public function prepare(WorkflowController $workflow, ?object $entity): void
    {
        // $workflow->setInitialStates(
        //     []
        // );

        $workflow->addTransition(
            'checkin',
            froms: [
                AttendState::BOOKED,
            ],
            to: AttendState::CHECKED_IN
        )
            ->button('far fa-fw fa-sign-in text-success', '簽到');

        $workflow->addTransition(
            'approve',
            froms: AttendState::PENDING,
            to: AttendState::BOOKED
        )
            ->button('far fa-fw fa-thumbs-up text-primary', '通過')
        ;

        $workflow->addTransition(
            'cancel',
            froms: [
                AttendState::PENDING,
                AttendState::BOOKED,
            ],
            to: AttendState::CANCEL
        )
            ->button('far fa-fw fa-xmark text-danger', '取消');
    }
}
