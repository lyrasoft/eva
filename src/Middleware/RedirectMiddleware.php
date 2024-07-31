<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\Redirect;
use App\Service\RedirectService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Http\AppRequest;
use Windwalker\Data\Collection;

use function Windwalker\collect;

class RedirectMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected RedirectService $redirectService,
        protected AppContext $app,
        protected AppRequest $appRequest,
        protected string|\BackedEnum|array|false|null $type = 'main',
        protected array|\Closure|null $list = null,
        protected bool $instantRedirect = false,
        protected array $ignores = [],
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->ignores) {
            $route = $this->appRequest->getSystemUri()->route;

            foreach ($this->ignores as $ignore) {
                if (fnmatch($ignore, $route)) {
                    return $handler->handle($request);
                }
            }
        }

        $redirects = collect();

        if ($this->type !== false) {
            $redirects = $redirects->merge($this->redirectService->getAvailableRedirects($this->type));
        }

        if ($this->list !== null) {
            $redirects = $redirects->merge($this->loadFromList());
        }

        [$deferRedirects, $currentRedirects] = $redirects->partition(
            fn(Redirect $redirect) => $redirect->isNotFoundOnly()
        );

        $res = $this->redirectService->matchAndRedirect($this->appRequest, $currentRedirects, $this->instantRedirect);

        if ($res) {
            return $res;
        }

        try {
            return $handler->handle($request);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                $res = $this->redirectService->matchAndRedirect(
                    $this->appRequest,
                    $deferRedirects,
                    $this->instantRedirect
                );

                if ($res) {
                    return $res;
                }
            }

            throw $e;
        }
    }

    protected function loadFromList(): Collection
    {
        if ($this->list instanceof \Closure) {
            $items = collect($this->list);
        } else {
            $items = collect($this->list);
        }

        $defaultStatus = env('REDIRECT_DEFAULT_STATUS') ?: 301;

        foreach ($items as $key => $item) {
            if (is_string($item)) {
                $r = new Redirect();
                $r->setSrc($key);
                $r->setDest($item);
                $r->setStatus((int) $defaultStatus);
                $r->setStatus(1);
                $r->setParams(
                    [
                        'regex' => '1',
                        'not_found_only' => '0',
                        'handle_lang' => '0',
                    ]
                );
                $items[$key] = $r;
            } elseif (!$item instanceof Redirect) {
                throw new \RuntimeException('Wrong format of redirect rules.');
            }
        }

        return $items->values();
    }
}
