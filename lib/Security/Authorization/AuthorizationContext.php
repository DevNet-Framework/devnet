<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Web\Security\Authorization;

use DevNet\System\Exceptions\PropertyException;
use DevNet\Web\Security\ClaimsPrincipal;

class AuthorizationContext
{
    private array $requirements;
    private ?ClaimsPrincipal $user;
    private bool $failCalled    = false;
    private bool $successCalled = false;

    public function __get(string $name)
    {
        if ($name == 'Requirements') {
            return $this->requirements;
        }

        if ($name == 'User') {
            return $this->user;
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property " . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property " . get_class($this) . "::" . $name);
    }

    public function __construct(array $requirements = [], ?ClaimsPrincipal $user = null)
    {
        $this->user = $user;
        foreach ($requirements as $requirement) {
            $this->requirements[spl_object_id($requirement)] = $requirement;
        }
    }

    public function fail()
    {
        $this->failCalled = true;
    }

    public function success(IAuthorizationRequirement $requirement)
    {
        $this->successCalled = true;
        if (isset($this->requirements[spl_object_id($requirement)])) {
            unset($this->requirements[spl_object_id($requirement)]);
        }
    }

    public function getResult(): AuthorizationResult
    {
        $status = 0;

        if (!$this->failCalled && $this->successCalled && !$this->requirements) {
            $status = 1;
        } else if ($this->failCalled) {
            $status = -1;
        }

        return new AuthorizationResult($status, $this->requirements);
    }
}
