<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Autoload;

use Includes\ClassPathResolver;
use Includes\ClassPathResolverInterface;

class ClassAutoLoader extends AbstractClassAutoLoader implements ClassAutoLoaderInterface
{
    /** @var ClassPathResolverInterface */
    private $targetClassPathResolver;

    public function __construct($compileDir)
    {
        $this->targetClassPathResolver = new ClassPathResolver($compileDir);
    }

    protected function load($class)
    {
        $pathname = $this->targetClassPathResolver->getPathname($class);

        if (file_exists($pathname)) {
            require_once $pathname;
        }
    }
}