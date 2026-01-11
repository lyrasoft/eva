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

use Windwalker\Core\Asset\AssetService;

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
        $asset = $this->app->retrieve(AssetService::class);

        $uri = $asset->resolveViteUri('resources/assets/src/front/test.ts');

        show($uri);

        return 0;
    }
}
