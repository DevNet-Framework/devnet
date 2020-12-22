<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Web\Dispatcher;

use Artister\Web\Dispatcher\RequestDelegate;
use Artister\Web\Dispatcher\IApplicationBuilder;
use Artister\Web\Dispatcher\IMiddleware;
use Artister\Web\Http\HttpContext;
use Artister\System\Dependency\IServiceProvider;
use Artister\System\Process\Task;
use Closure;

class ApplicationBuilder implements IApplicationBuilder
{
    use \Artister\System\Extension\ExtensionTrait;

    private IserviceProvider $Provider;
    private MiddlewareFactory $MiddlewareFactoty;
    private array $Middlewares;

    public function __construct(IServiceProvider $provider)
    {
        $this->Provider = $provider;
        $this->MiddlewareFactoty = new MiddlewareFactory($provider);
        $this->Middlewares = [];
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    /**
     * @param IMiddleware | Closure | string $middleware
     */
    public function use($middleware)
    {
        if (is_object($middleware))
        {
            if ($middleware instanceof Closure)
            {
                $middleware = new RequestDelegate($middleware);

            }
            else if (!$middleware instanceof IMiddleware)
            {
                throw new \Exception("invalide type, class must be of type Artister\System\Web\Hosting\IMiddleware");
            }
        }

        if (is_string($middleware))
        {
            $middleware = $this->MiddlewareFactoty->create($middleware);
        }

        $this->Middlewares[] = $middleware;
    }

    public function pipe(callable $middleware, RequestDelegate $next)
    {
        return new RequestDelegate(function (HttpContext $context) use ($middleware, $next) : Task {
            return $middleware($context, $next);
        });
    }

    public function Build() : RequestDelegate
    {
        $app = new RequestDelegate(function(HttpContext $context) : Task {

            $RequestHandler = $context->Handler;

            if ($RequestHandler)
            {
                throw new \Exception("The request has reached the end of the pipeline without being executed the endpoint");
            }

            $context->Response->setStatusCode(404);
            return Task::completedTask();
        });

        foreach(array_reverse($this->Middlewares) as $middleware)
        {
            $app = $this->pipe($middleware, $app);
        }

        return $app;
    }
}