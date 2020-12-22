<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Web\Hosting;

use Closure;

interface IWebHostBuilder
{
    public function configureServices(Closure $configureServices);

    public function configureApplication(Closure $configureApp);

    public function build() : WebHost;
}