<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Admin;

use Lyrasoft\Luna\Script\FontAwesomeScript;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Unicorn\Script\UnicornScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Middleware\AbstractLifecycleMiddleware;

/**
 * The FrontMiddleware class.
 */
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

    /**
     * prepareExecute
     *
     * @param ServerRequestInterface $request
     *
     * @return  mixed
     */
    protected function preprocess(ServerRequestInterface $request): void
    {
        $this->lang->loadAllFromVendor('windwalker/unicorn', 'ini');
        $this->lang->loadAllFromVendor('lyrasoft/luna', 'ini');
        $this->lang->loadAllFromVendor('lyrasoft/member', 'ini');
        $this->lang->loadAllFromVendor('lyrasoft/portfolio', 'ini');
        $this->lang->loadAll('ini');

        // Unicorn
        $this->unicornScript->init('js/admin/main.js');

        // Font Awesome
        $this->fontAwesomeScript->cssFont(FontAwesomeScript::PRO | FontAwesomeScript::DEFAULT_SET | FontAwesomeScript::LIGHT);

        // Bootstrap
        $this->asset->css('css/admin/bootstrap.min.css');
        $this->asset->js('vendor/bootstrap/dist/js/bootstrap.bundle.min.js');

        // Theme
        $this->asset->js('vendor/jquery/dist/jquery.min.js');
        $this->asset->js('vendor/admin/metismenu/metisMenu.min.js');
        $this->asset->js('vendor/admin/simplebar/simplebar.min.js');
        $this->asset->js('vendor/admin/node-waves/waves.min.js');
        $this->asset->js('js/admin/app.min.js');
        $this->asset->css('css/admin/app.min.css');
        $this->asset->css('css/admin/icons.min.css');

        // Main
        $this->asset->css('css/admin/main.css');

        // Meta
        $this->htmlFrame->setFavicon($this->asset->path('images/admin/favicon.png'));
    }

    // protected function checkAccess(): bool
    // {
    //     $user = $this->app->service(UserService::class)->getUser();
    //
    //     return $user->isLogin();
    // }
    //
    // public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    // {
    //     $user = $this->app->service(UserService::class)->getUser();
    //
    //     if (!$user->isLogin()) {
    //         return $this->app->service(Navigator::class)->redirectTo('front::home');
    //     }
    //
    //     return parent::process($request, $handler);
    // }

    /**
     * postExecute
     *
     * @param ResponseInterface $response
     *
     * @return  mixed
     */
    protected function postProcess(ResponseInterface $response): void
    {
    }
}
