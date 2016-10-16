<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes;

class ClassPathResolver implements ClassPathResolverInterface
{
    private $classDir;

    public function __construct($classDir)
    {
        $this->classDir = rtrim($classDir, '\\/') . LC_DS;
    }

    public function getPathname($class)
    {
        return $this->classDir . str_replace('\\', LC_DS, $class) . '.php';
    }

    public function getClass($pathname)
    {
        return str_replace('/', '\\', substr($pathname, strlen($this->classDir), -4));
    }

    public function getFullPath($subPath)
    {
        return $this->classDir . ltrim($subPath, '\\/');
    }

    public function getRelativePath($fullPath)
    {
        return substr($fullPath, strlen($this->classDir));
    }
}