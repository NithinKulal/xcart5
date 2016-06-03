<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder;

use Iterator;
use IteratorAggregate;

interface ModuleRegistryInterface
{
    public function has($module);

    public function hasAll(array $modules);

    public function hasNone(array $modules);

    /**
     * @return ModuleInterface[]
     */
    public function getModules();
}