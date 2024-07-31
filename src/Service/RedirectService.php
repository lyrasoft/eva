<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Redirect;
use App\Repository\RedirectRepository;
use Lyrasoft\Luna\Services\LocaleService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Application\Context\AppRequestInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\DI\Attributes\Service;
use Windwalker\Http\Response\RedirectResponse;
use Windwalker\Uri\UriHelper;
use Windwalker\Utilities\Str;

use function Windwalker\response;

#[Service]
class RedirectService
{
    public function __construct(
        #[Autowire]
        protected RedirectRepository $repository,
        protected AppContext $app,
        protected LocaleService $localeService,
    ) {
        //
    }

    public function matchFromList(AppRequestInterface $request, iterable $redirects, ?array &$matches = null): ?Redirect
    {
        $route = $request->getSystemUri()->route();
        $route = rtrim($route, '/');

        /** @var Redirect $redirect */
        foreach ($redirects as $redirect) {
            $matched = $this->matchSingleRedirect($route, $redirect, $matches);

            if ($matched) {
                return $matched;
            }
        }

        return null;
    }

    public function matchSingleRedirect(string $route, Redirect $redirect, ?array &$matches = null): ?Redirect
    {
        // Do not modify origin object
        $redirect = clone $redirect;

        $src = rtrim($redirect->getSrc(), '/');

        // Handle lang
        $handleLang = $redirect->isHandleLang() && $this->localeService->isEnabled();
        $langAlias = null;

        if ($handleLang) {
            $route = $this->stripLangAlias($route, $langAlias);
        }

        $destRedirect = null;

        if ($redirect->isRegexEnabled()) {
            $src = $this->parseWildcards($src);

            $regex = '/';

            // Is absolute
            if (str_starts_with($src, '/')) {
                $regex .= '^';
                $route = Str::ensureLeft($route, '/');
            }

            $regex .= str_replace('/', '\\/', $src) . '/';

            if (preg_match($regex, $route, $matches)) {
                $destRedirect = $redirect;
            }
        } elseif (Str::ensureLeft($route, '/') === Str::ensureLeft($src, '/')) {
            $destRedirect = $redirect;
        }

        if (!$destRedirect) {
            return null;
        }

        if ($handleLang) {
            $dest = $destRedirect->getDest();

            if (str_contains($dest, '{lang}')) {
                $dest = str_replace('{lang}', (string) $langAlias, $dest);
            } elseif ($langAlias && !UriHelper::isAbsolute($dest)) {
                $dest = $langAlias . '/' . $dest;
            }

            $destRedirect->setDest($dest);
        }

        return $destRedirect;
    }

    /**
     * @param  AppRequestInterface  $request
     * @param  iterable<Redirect>   $redirects
     * @param  bool                 $instant
     *
     * @return  RedirectResponse|null
     */
    public function matchAndRedirect(
        AppRequestInterface $request,
        iterable $redirects,
        bool $instant = false
    ): ?RedirectResponse {
        $redirect = $this->matchFromList($request, $redirects, $matches);

        if (!$redirect) {
            return null;
        }

        $dest = $redirect->getDest();

        if ($redirect->isRegexEnabled()) {
            $dest = $this->replaceVariables($dest, (array) $matches);
        }

        if ($instant) {
            $this->app->redirect($dest, $redirect->getStatus(), true);
            die;
        }

        return response()->redirect($dest, $redirect->getStatus());
    }

    /**
     * @param  string|\BackedEnum|array|null  $type
     *
     * @return  Collection<Redirect>
     */
    public function getAvailableRedirects(string|\BackedEnum|array|null $type): Collection
    {
        return $this->repository->getAvailableSelector($type)
            ->all(Redirect::class);
    }

    protected function parseWildcards(string $src): string
    {
        $src = str_replace(
            ['\\**', '\\*'],
            ['__DWC__', '__WC__'],
            $src
        );

        $src = (string) str_replace(
            '**',
            '(.+)',
            $src
        );

        $src = (string) str_replace(
            '*',
            '([^/]+)',
            $src
        );

        $src = str_replace(
            ['__DWC__', '__WC__'],
            ['\\**', '\\*'],
            $src
        );

        return $src;
    }

    public function replaceVariables(string $dest, array $matches): string
    {
        foreach ($matches as $i => $value) {
            $key = '$' . $i;

            $dest = str_replace($key, $value, $dest);
        }

        return $dest;
    }

    protected function stripLangAlias(string $route, ?string &$alias = null): string
    {
        $langs = $this->localeService->getAvailableLanguages();

        foreach ($langs as $lang) {
            if (str_starts_with($route, $lang->getAlias())) {
                $route = Str::removeLeft($route, $lang->getAlias());
                $route = Str::removeLeft($route, '/') ?: '/';

                $alias = $lang->getAlias();

                return $route;
            }
        }

        return $route;
    }
}
