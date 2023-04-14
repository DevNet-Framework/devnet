<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Web\Action;

use DevNet\System\Action;
use DevNet\System\Async\AsyncFunction;
use DevNet\System\Async\Task;
use DevNet\Web\Action\ActionContext;
use DevNet\Web\Action\Binder\IValueProvider;
use DevNet\Web\Action\Binder\ParameterBinder;
use DevNet\Web\Http\HttpContext;
use DevNet\Web\Middleware\IRequestHandler;

class ActionInvoker implements IRequestHandler
{
    private ActionDescriptor $actionDescriptor;
    private IValueProvider $provider;
    private ActionDelegate $action;
    private array $filters = [];

    public function __construct(ActionDescriptor $actionDescriptor, IValueProvider $provider)
    {
        $this->actionDescriptor = $actionDescriptor;
        $this->provider = $provider;
        $this->filters = $actionDescriptor->FilterAttributes;
    }

    public function createInstance(ActionContext $actionContext): object
    {
        $classInfo   = $actionContext->ActionDescriptor->ClassInfo;
        $constructor = $classInfo->getConstructor();
        $instance    = $classInfo->newInstanceWithoutConstructor();

        if (property_exists($instance, 'ActionContext')) {
            $instance->ActionContext = $actionContext;
        }

        if (property_exists($instance, 'HttpContext')) {
            $instance->HttpContext = $actionContext->HttpContext;
        }

        if (!$constructor) {
            return $instance;
        }

        $services   = $actionContext->HttpContext->RequestServices;
        $parameters = $constructor->getParameters();
        $arguments  = [];

        foreach ($parameters as $parameter) {
            $parameterType = '';
            if ($parameter->getType()) {
                $parameterType = $parameter->getType()->getName();
            }

            if (!$services->contains($parameterType)) {
                break;
            }

            $arguments[] = $services->getService($parameterType);
        }

        $constructor->invokeArgs($instance, $arguments);

        return $instance;
    }

    public function getNextFilter(): ?IActionFilter
    {
        $attribute = array_shift($this->filters);
        if ($attribute) {
            return $attribute->newInstance();
        }

        return null;
    }

    public function __invoke(HttpContext $httpContext)
    {
        $actionContext = new ActionContext($this->actionDescriptor, $httpContext, $this->provider);
        $instance = $this->createInstance($actionContext);
        $this->action = new ActionDelegate(function (ActionContext $context) use ($instance) {
            $actionFilter = $this->getNextFilter();
            if ($actionFilter) {
                return $actionFilter($context, $this->action);
            }

            $parameterBinder = new ParameterBinder();
            $arguments = $parameterBinder->resolveArguments($context);
            $action = new Action([$instance, $context->ActionDescriptor->ActionName]);

            return Task::run(function () use ($action, $context, $arguments) {
                $actionResult = yield $action->invokeArgs($arguments);
                $result = yield $actionResult($context);
                return $result;
            });
        });

        return $this->action->invoke($actionContext);
    }
}
