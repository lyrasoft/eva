<?php

declare(strict_types=1);

namespace App\Module\Front;

use Lyrasoft\Banner\BannerPackage;
use Lyrasoft\Contact\ContactPackage;
use Lyrasoft\EventBooking\EventBookingPackage;
use Lyrasoft\Favorite\FavoritePackage;
use Lyrasoft\Feedback\FeedbackPackage;
use Lyrasoft\Firewall\FirewallPackage;
use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\Script\FontAwesomeScript;
use Lyrasoft\Luna\Services\ConfigService;
use Lyrasoft\Member\MemberPackage;
use Lyrasoft\Portfolio\PortfolioPackage;
use Lyrasoft\ShopGo\Script\ShopGoScript;
use Lyrasoft\ShopGo\ShopGoPackage;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Unicorn\Script\UnicornScript;
use Unicorn\UnicornPackage;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Middleware\AbstractLifecycleMiddleware;
use Windwalker\DI\Exception\DefinitionException;

class FrontMiddleware extends AbstractLifecycleMiddleware
{
    use TranslatorTrait;

    public function __construct(
        protected AppContext $app,
        protected AssetService $asset,
        protected HtmlFrame $htmlFrame,
        protected UnicornScript $unicornScript,
        protected FontAwesomeScript $fontAwesomeScript,
        protected ShopGoScript $shopGoScript,
    ) {
    }

    /**
     * prepareExecute
     *
     * @param  ServerRequestInterface  $request
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws DefinitionException
     */
    protected function preprocess(ServerRequestInterface $request): void
    {
        $this->lang->loadAllFromVendor(UnicornPackage::class, 'ini');
        $this->lang->loadAllFromVendor(LunaPackage::class, 'ini');
        $this->lang->loadAllFromVendor(MemberPackage::class, 'ini');
        $this->lang->loadAllFromVendor(PortfolioPackage::class, 'ini');
        $this->lang->loadAllFromVendor(ShopGoPackage::class, 'ini');
        $this->lang->loadAllFromVendor(ContactPackage::class, 'ini');
        $this->lang->loadAllFromVendor(BannerPackage::class, 'ini');
        $this->lang->loadAllFromVendor(ContactPackage::class, 'ini');
        $this->lang->loadAllFromVendor(EventBookingPackage::class, 'ini');
        $this->lang->loadAllFromVendor(FavoritePackage::class, 'ini');
        $this->lang->loadAllFromVendor(FirewallPackage::class, 'ini');
        $this->lang->loadAllFromVendor(FeedbackPackage::class, 'ini');

        $this->lang->loadAll('ini');

        // Unicorn
        $this->unicornScript->init('@vite/src/front/main.ts');

        // Font Awesome
        $this->fontAwesomeScript->cssFont(
            FontAwesomeScript::PRO | FontAwesomeScript::DEFAULT_SET | FontAwesomeScript::LIGHT
        );

        // Main
        $this->asset->css('@vite/scss/front/bootstrap.scss');
        $this->asset->css('@vite/scss/front/main.scss');

        // Metadata
        $coreConfig = $this->app->service(ConfigService::class)->getConfig('core');

        // ShopGo
        $this->shopGoScript->currencySwitcher();

        $this->htmlFrame->setFavicon('@vite/images/favicon.png');
        $this->htmlFrame->setSiteName('Windwalker');
        $this->htmlFrame->setDescription('Windwalker Site Description.');
        // $this->htmlFrame->setCoverImages($this->asset->root('...'));

        if ($sc = trim((string) $coreConfig->get('google_search_console'))) {
            $this->htmlFrame->addMetadata('google-site-verification', $sc);
        }
    }

    protected function postProcess(ResponseInterface $response): void
    {
    }
}
