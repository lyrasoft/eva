<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\IpRule;
use App\Repository\IpRuleRepository;
use App\Service\FirewallService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Windwalker\Core\Application\Context\AppRequestInterface;
use Windwalker\DI\Attributes\Autowire;

use function Windwalker\response;

class IpBlockMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected AppRequestInterface $appRequest,
        #[Autowire]
        protected FirewallService $firewallService,
        protected IpRuleRepository $repository,
        protected bool $enabled = true,
        protected string|\BackedEnum|array|false|null $type = 'main',
        protected array $allowList = [],
        protected array $blockList = [],
        protected array $ignores = [],
    ) {
        //
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

        if ($this->enabled) {
            $currentIP = $this->appRequest->getClientIP();

            if ($this->type !== false) {
                $rules = $this->repository->getFrontListSelector($this->type)
                    ->all(IpRule::class);

                [$allows, $blocks] = $this->firewallService->getAllowAndBlockList($this->type);

                $this->allowList = array_merge($this->allowList, $allows);
                $this->blockList = array_merge($this->blockList, $blocks);
            }

            if (!$this->firewallService->isAllow($currentIP, $this->allowList, $this->blockList)) {
                return response('', 403);
            }
        }

        return $handler->handle($request);
    }

    public function getAllowList(): array
    {
        return $this->allowList;
    }

    public function setAllowList(array $allowList): static
    {
        $this->allowList = $allowList;

        return $this;
    }

    public function getBlockList(): array
    {
        return $this->blockList;
    }

    public function setBlockList(array $blockList): static
    {
        $this->blockList = $blockList;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
}
