<?php

declare(strict_types=1);

namespace App\view;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use Lyrasoft\Luna\Entity\Article;
use Lyrasoft\Luna\Entity\Page;
use Lyrasoft\Luna\PageBuilder\PageBuilder;
use Lyrasoft\ShopGo\Entity\ProductTab;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\ORM\ORM;

use function Windwalker\DOM\div;

/**
 * @var $tab ProductTab
 */

$orm = $app->service(ORM::class);

$content = '';

if ($tab->getArticleId()) {
    $article = $orm->findOne(Article::class, ['id' => $tab->getArticleId(), 'state' => 1]);

    if ($article) {
        $content = $article->getIntrotext() . $article->getFulltext();
    }
} elseif ($tab->getPageId()) {
    $page = $orm->findOne(Page::class, ['id' => $tab->getPageId(), 'state' => 1]);
    $pageBuilder = $app->service(PageBuilder::class);
    $content = $pageBuilder->renderPage($page->getContent());
    $content = "<div class=\"l-page-container\">$content</div>";

    $asset->internalJS($page->getCss());
} else {
    $content = $tab->getContent();
}

echo $content;
