<?php

declare(strict_types=1);

namespace App\Command;

use Lyrasoft\Melo\Data\AddressInfo;
use Lyrasoft\Melo\Data\InvoiceData;
use Lyrasoft\Melo\Entity\MeloOrder;
use Lyrasoft\Throttle\Enum\RateLimitPolicy;
use Lyrasoft\Throttle\Service\ThrottleService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\RateLimiter\Policy\Rate;
use Windwalker\Console\CommandInterface;
use Windwalker\Console\CommandWrapper;
use Windwalker\Console\IOInterface;
use Windwalker\Core\Application\ApplicationInterface;

use Windwalker\Core\Asset\AssetService;

use Windwalker\ORM\ORM;

use function Windwalker\collect;

#[CommandWrapper(
    description: ''
)]
class PlayCommand implements CommandInterface
{
    public function __construct(protected ORM $orm, protected ApplicationInterface $app)
    {
    }

    public function configure(Command $command): void
    {
        //
    }

    public function execute(IOInterface $io): int
    {
        $item = new MeloOrder();
        $item->invoiceData = new InvoiceData(
            name: 'John Doe',
            title: 'Mr.',
            vat: '12345678',
            carrier: 'XYZ123',
            address: new AddressInfo(
                city: 'New York',
                dist: 'Manhattan',
                zip: '10001',
                address: '123 5th Ave'
            )
        );

        show(
            $this->orm->extractEntity($item),
        );

        return 0;
    }
}
