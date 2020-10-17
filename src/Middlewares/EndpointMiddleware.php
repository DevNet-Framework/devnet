<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\DevNet\Middlewares;

use Artister\DevNet\Dispatcher\IMiddleware;
use Artister\DevNet\Dispatcher\RequestDelegate;
use Artister\System\Web\Http\HttpContext;
use Artister\System\Process\Task;

class EndpointMiddleware implements IMiddleware
{
    public function __invoke(HttpContext $context, RequestDelegate $next) : Task
    {
        $requestHandler = $context->Handler;

        if (!$requestHandler)
        {
            return $next($context);
        }

        return $requestHandler($context);
    }
}