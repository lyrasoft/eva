<?php

declare(strict_types=1);

namespace App\Module\Admin;

use Lyrasoft\ActionLog\ActionLogPackage;
use Lyrasoft\Banner\BannerPackage;
use Lyrasoft\Contact\ContactPackage;
use Lyrasoft\EventBooking\EventBookingPackage;
use Lyrasoft\Favorite\FavoritePackage;
use Lyrasoft\Feedback\FeedbackPackage;
use Lyrasoft\Firewall\FirewallPackage;
use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\Script\FontAwesomeScript;
use Lyrasoft\Member\MemberPackage;
use Lyrasoft\Portfolio\PortfolioPackage;
use Lyrasoft\ShopGo\ShopGoPackage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Unicorn\Script\UnicornScript;
use Unicorn\UnicornPackage;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Middleware\AbstractLifecycleMiddleware;

class AdminMiddleware extends AbstractLifecycleMiddleware
{
    use TranslatorTrait;

    public function __construct(
        protected AppContext $app,
        protected AssetService $asset,
        protected UnicornScript $unicornScript,
        protected FontAwesomeScript $fontAwesomeScript,
        protected HtmlFrame $htmlFrame,
    ) {
    }

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
        $this->lang->loadAllFromVendor(ActionLogPackage::class, 'ini');

        $this->lang->loadAll('ini');

        // Unicorn
        $this->unicornScript->init('@vite/src/admin/main.ts');

        // Font Awesome
        $this->fontAwesomeScript->cssFont(
            FontAwesomeScript::PRO | FontAwesomeScript::DEFAULT_SET | FontAwesomeScript::LIGHT
        );

        // Theme
        $this->asset->js('vendor/nexus/libs/ribble/dist/ribble.js');
        $this->asset->css('@vite/scss/admin/nexus.scss');

        // Main
        $this->asset->css('@vite/scss/admin/main.scss');

        // HtmlFrame
        $this->htmlFrame->setFavicon('@vite/images/admin/favicon.png');
    }

    protected function postProcess(ResponseInterface $response): void
    {
    }
}
