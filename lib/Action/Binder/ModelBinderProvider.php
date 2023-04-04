<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Web\Action\Binder;

use IteratorAggregate;
use ArrayIterator;
use Traversable;

class ModelBinderProvider implements IteratorAggregate
{
    private array $modelBinders = [];

    public function __construct(IModelBinder $modelBinder = null)
    {
        if ($modelBinder) {
            $this->modelBinders[] = $modelBinder;
        }
    }

    public function add(IModelBinder $modelBinder)
    {
        $this->modelBinders[] = $modelBinder;
        return $this;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->modelBinders);
    }
}