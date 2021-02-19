<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Web\Security\Antiforgery;

use Artister\Web\Http\CookieOptions;

class AntiforgeryOptions
{
    public CookieOptions $Cookie;
    public string $CookieName;
    public string $FormFieldName;

    public function __construct()
    {
        $this->Cookie = new CookieOptions();
        $this->CookieName = "DevNet-Antiforgery";
        $this->FormFieldName = "AntiforgeryToken";
    }
}