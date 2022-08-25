<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Web\Hosting;

use DevNet\Web\Middleware\RequestDelegate;

interface IApplicationBuilder
{
    /**
     * @param IMiddleware | Closure | string $middleware
     */
    public function use(callable $middleware): void;

    public function build(): RequestDelegate;
}