<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder;

use Includes\Autoload\DecoratedAncestorStreamWrapper;
use Includes\Decorator\ClassBuilder\DependencyExtractor\CachingDependencyExtractor;
use Includes\ClassPathResolver;
use Includes\Autoload\StreamWrapper;
use Includes\Decorator\ClassBuilder\ClassBuilder;
use Includes\Decorator\ClassBuilder\DependencyExtractor\DependencyExtractor;
use Includes\Decorator\ClassBuilder\ModuleRegistry;
use Includes\SourceToTargetPathMapper;
use Includes\Reflection\StaticReflectorFactory;

class ClassBuilderFactory
{
    /**
     * @param       $classDir
     * @param       $compileDir
     * @param array $modules
     *
     * @return ClassBuilderInterface
     */
    public function create($classDir, $compileDir, array $modules)
    {
        // TODO: Outsource dependency instantiation to IoC container

        $sourceClassPathResolver        = new ClassPathResolver($classDir);
        $targetClassPathResolver        = new ClassPathResolver($compileDir);
        $sourceToTargetPathMapper       = new SourceToTargetPathMapper($sourceClassPathResolver, $targetClassPathResolver);
        $sourceStaticReflectorFactory   = new StaticReflectorFactory($sourceClassPathResolver);
        $streamWrapper                  = new StreamWrapper($sourceToTargetPathMapper);
        $decoratedAncestorStreamWrapper = new DecoratedAncestorStreamWrapper($sourceToTargetPathMapper);
        $moduleRegistry                 = new ModuleRegistry($sourceClassPathResolver, $modules);

        $decoratorExtractor = new CachingDependencyExtractor(
            new DependencyExtractor(
                $sourceClassPathResolver,
                $targetClassPathResolver,
                $sourceStaticReflectorFactory,
                $moduleRegistry
            ),
            $targetClassPathResolver
        );

        return new ClassBuilder(
            $sourceClassPathResolver,
            $targetClassPathResolver,
            $sourceToTargetPathMapper,
            $streamWrapper,
            $decoratedAncestorStreamWrapper,
            $sourceStaticReflectorFactory,
            $decoratorExtractor,
            $moduleRegistry
        );
    }
}