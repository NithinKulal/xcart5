<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Autoload;

use Includes\Decorator\ClassBuilder\ClassBuilderFactory;
use Includes\Decorator\ClassBuilder\ClassBuilderInterface;

class DevClassAutoLoader extends AbstractClassAutoLoader implements ClassAutoLoaderInterface
{
    /** @var ClassBuilderInterface */
    private $classBuilder;

    public function __construct($classDir, $compileDir, array $modules)
    {
        $this->classBuilder = (new ClassBuilderFactory())->create($classDir, $compileDir, $modules);
    }

    protected function load($class)
    {
        if (($stream = $this->classBuilder->buildClassname($class)) !== null) {
            require_once $stream;
        }
    }
}
