<?php

declare(strict_types=1);

namespace App\Command;

use Lyrasoft\Throttle\Enum\RateLimitPolicy;
use Lyrasoft\Throttle\Service\ThrottleService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\RateLimiter\Policy\Rate;
use Windwalker\Console\CommandInterface;
use Windwalker\Console\CommandWrapper;
use Windwalker\Console\IOInterface;
use Windwalker\Core\Application\ApplicationInterface;

use function Windwalker\collect;

#[CommandWrapper(
    description: ''
)]
class PlayCommand implements CommandInterface
{
    public function __construct(protected ApplicationInterface $app)
    {
    }

    public function configure(Command $command): void
    {
        //
    }

    public function execute(IOInterface $io): int
    {
        $throttleService = $this->app->retrieve(ThrottleService::class);

        $limiter = $throttleService->createRateLimiter(
            'hello',
            policy: RateLimitPolicy::FIXED_WINDOW,
            limit: 5,
            interval: '1minutes',
        );

        show($limiter->consume(1));

        return 0;
    }
}
