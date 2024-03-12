<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Core\Endpoint;

use DevNet\System\Async\Task;

interface IActionResult
{
    public function __invoke(ActionContext $actionContext): Task;
}
