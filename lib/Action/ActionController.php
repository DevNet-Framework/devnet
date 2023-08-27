<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Web\Action;

use DevNet\System\Exceptions\ArgumentException;
use DevNet\System\Tweak;
use DevNet\Web\Action\Results\ContentResult;
use DevNet\Web\Action\Results\JsonResult;
use DevNet\Web\Action\Results\RedirectResult;
use DevNet\Web\Action\Results\StatusCodeResult;
use DevNet\Web\Action\Results\ViewResult;
use DevNet\Web\Action\Features\HtmlHelper;
use DevNet\Web\Http\HttpContext;
use DevNet\Web\View\ViewManager;

abstract class ActionController
{
    use Tweak;

    public ActionContext $ActionContext;
    public HttpContext $HttpContext;
    public array $ViewData = [];

    public function view($parameter = null, object $model = null): ViewResult
    {
        if (!$parameter) {
            $viewName = null;
        } else if (is_string($parameter)) {
            $viewName = $parameter;
        } else if (is_object($parameter)) {
            $viewName = null;
            $model    = $parameter;
        } else {
            throw new ArgumentException(static::class . "::view(): The argument 1# must be of type string or object", 0, 1);
        }

        if (!$viewName) {
            $prefix         = $this->HttpContext->RouteContext->RouteData->Values['prefix'];
            $controllerName = $this->ActionContext->ActionDescriptor->ClassName;
            $controllerName = str_replace('Controller', '', $this->ActionContext->ActionDescriptor->ClassName);
            $actionName     = $this->ActionContext->ActionDescriptor->ActionName;

            if (strtolower(strstr($actionName, "_", true)) == "async") {
                $actionName = substr(strstr($actionName, "_"), 1);
            }

            $viewName = ucwords($prefix . $controllerName, '/') . '/' . $actionName;
        }

        $view = $this->HttpContext->RequestServices->getService(ViewManager::class);
        $view->setData($this->ViewData);
        $view->inject('Html', new HtmlHelper($this->HttpContext));
        return new ViewResult($view($viewName, $model), 200);
    }

    public function content(string $content, string $contentType = 'text/plain'): ContentResult
    {
        return new ContentResult($content, $contentType);
    }

    public function json(object|array $data, $statusCode = 200): JsonResult
    {
        return new JsonResult($data, $statusCode);
    }

    public function redirect(string $path): RedirectResult
    {
        return new RedirectResult($path);
    }

    public function statusCode(int $code): StatusCodeResult
    {
        return new StatusCodeResult($code);
    }

    public function unauthorized(): StatusCodeResult
    {
        return $this->statusCode(401);
    }

    public function forbid(): StatusCodeResult
    {
        return $this->statusCode(403);
    }

    public function notFound(): StatusCodeResult
    {
        return $this->statusCode(404);
    }

    public function ok(): StatusCodeResult
    {
        return $this->statusCode(200);
    }
}