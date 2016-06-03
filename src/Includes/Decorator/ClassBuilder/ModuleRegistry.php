<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder;

use Includes\ClassPathResolverInterface;

class ModuleRegistry implements ModuleRegistryInterface
{
    /**
     * @var array
     */
    private $modules;

    /**
     * @var ClassPathResolverInterface
     */
    private $sourceClassPathResolver;

    public function __construct(ClassPathResolverInterface $sourceClassPathResolver, array $modules)
    {
        $this->sourceClassPathResolver = $sourceClassPathResolver;
        $this->modules                 = array_combine($modules, $modules);
    }

    public function has($module)
    {
        return isset($this->modules[$module]);
    }

    public function hasAll(array $modules)
    {
        return array_reduce($modules, function ($hasAll, $modules) {
            return $hasAll && isset($this->modules[$modules]);
        }, true);
    }

    public function hasNone(array $modules)
    {
        return array_reduce($modules, function ($hasNone, $modules) {
            return $hasNone && !isset($this->modules[$modules]);
        }, true);
    }

    public function getModules()
    {
        $modules = [];

        foreach ($this->modules as $module) {
            $modules[] = new Module($this->sourceClassPathResolver, $module);
        }

        return $modules;
    }
}