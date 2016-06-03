<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder;

use Includes\ClassPathResolverInterface;

class Module implements ModuleInterface
{
    /**
     * @var string Module name
     */
    private $module;

    /**
     * @var ClassPathResolverInterface
     */
    private $sourceClassPathResolver;

    public function __construct(ClassPathResolverInterface $sourceClassPathResolver, $module)
    {
        $this->sourceClassPathResolver = $sourceClassPathResolver;
        $this->module                  = $module;
    }

    public function getName()
    {
        return $this->module;
    }

    public function getPath()
    {
        return $this->sourceClassPathResolver->getFullPath('XLite/Module/' . str_replace('\\', '/', $this->module));
    }
}