<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Web\Security\Authorization;

use DevNet\Web\Security\Claims\ClaimsIdentity;

interface IAuthorization
{
    public function authorize(ClaimsIdentity $user, ?string $policyName): AuthorizationResult;
}