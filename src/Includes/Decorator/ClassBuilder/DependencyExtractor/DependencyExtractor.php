<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder\DependencyExtractor;

use Includes\ClassPathResolverInterface;
use Includes\Decorator\ClassBuilder\DependencyExtractor\DependencyExtractorInterface;
use Includes\Reflection\StaticReflectorInterface;
use MJS\TopSort\Implementations\StringSort;
use Includes\Decorator\ClassBuilder\ModuleInterface;
use Includes\Decorator\ClassBuilder\ModuleRegistryInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Includes\Reflection\StaticReflectorFactoryInterface;

class DependencyExtractor implements DependencyExtractorInterface
{
    /**
     * @var ClassPathResolverInterface
     */
    private $sourceClassPathResolver;

    /**
     * @var ModuleRegistryInterface
     */
    private $moduleRegistry;

    /**
     * @var StaticReflectorFactoryInterface
     */
    private $reflectorFactory;

    /**
     * @var ClassPathResolverInterface
     */
    private $targetClassPathResolver;

    private $decoratorCandidates;

    private $decorators;

    private $decoratorsByClass;

    public function __construct(
        ClassPathResolverInterface $sourceClassPathResolver,
        ClassPathResolverInterface $targetClassPathResolver,
        StaticReflectorFactoryInterface $sourceStaticReflectorFactory,
        ModuleRegistryInterface $moduleRegistry
    ) {
        $this->sourceClassPathResolver = $sourceClassPathResolver;
        $this->targetClassPathResolver = $targetClassPathResolver;
        $this->moduleRegistry          = $moduleRegistry;
        $this->reflectorFactory        = $sourceStaticReflectorFactory;
    }

    public function getDecoratorCandidates()
    {
        if (!isset($this->decoratorCandidates)) {
            $decorators = [];

            foreach ($this->moduleRegistry->getModules() as $module) {
                $decorators = array_merge(
                    $decorators,
                    $this->searchDirsRecursively($module->getPath(), '/^.+\.php$/')
                );
            }

            $this->decoratorCandidates = $decorators;
        }

        return $this->decoratorCandidates;
    }

    public function getDecorators()
    {
        if (!isset($this->decorators)) {
            $this->decorators = array_filter($this->getDecoratorCandidates(), function ($decorator) {
                return $this->reflectorFactory->reflectSource($decorator)->isDecorator();
            });
        }

        return $this->decorators;
    }

    public function getClassDecorators($class)
    {
        $decorators = array_filter($this->getClassDecoratorCandidates($class), function ($decorator) use ($class) {
            $reflector = $this->reflectorFactory->reflectSource($decorator);

            return $this->moduleRegistry->hasAll($reflector->getPositiveDependencies())
                   && $this->moduleRegistry->hasNone($reflector->getNegativeDependencies())
                   && (LC_DEVELOPER_MODE || $reflector->isPSR0());
        });

        return !empty($decorators) ? $this->sortDecorators($decorators) : [];
    }

    public function getClassDecoratorCandidates($class)
    {
        if (!isset($this->decoratorsByClass)) {
            $this->decoratorsByClass = [];

            foreach ($this->getDecorators() as $decorator) {
                $reflector = $this->reflectorFactory->reflectSource($decorator);

                $parent = $reflector->getParent();

                if (!isset($this->decoratorsByClass[$parent])) {
                    $this->decoratorsByClass[$parent] = [];
                }

                $this->decoratorsByClass[$parent][] = $decorator;
            }
        }

        return isset($this->decoratorsByClass[$class]) ? $this->decoratorsByClass[$class] : [];
    }

    public function areClassDecoratorsChanged($class)
    {
        return true;
    }

    private function searchDirsRecursively($folder, $pattern)
    {
        if (!is_dir($folder)) {
            return [];
        }

        $dir   = new RecursiveDirectoryIterator($folder);
        $ite   = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);

        $fileList = [];
        foreach ($files as $file) {
            $fileList = array_merge($fileList, $file);
        }

        return $fileList;
    }

    private function sortDecorators($decorators)
    {
        $modules = $modulesDeps = [];

        foreach ($decorators as $file) {
            $reflector = $this->reflectorFactory->reflectSource($file);
            $module    = $reflector->getModule();

            if (!isset($modules[$module])) {
                $modules[$module] = [];
            }
            $modules[$module][] = $file;

            foreach ($reflector->getBeforeModules() as $before) {
                if (!isset($modulesDeps[$before])) {
                    $modulesDeps[$before] = [];
                }

                $modulesDeps[$before][] = $file;
            }
        }

        $sorter = new StringSort();

        foreach ($decorators as $file) {
            $reflector = $this->reflectorFactory->reflectSource($file);
            $module    = $reflector->getModule();

            $deps = [];

            foreach ($reflector->getAfterModules() as $after) {
                if (isset($modules[$after])) {
                    $deps = array_merge($deps, array_diff($modules[$after], [$file]));
                }
            }

            if (isset($modulesDeps[$module])) {
                $deps = array_merge($deps, array_diff($modulesDeps[$module], [$file]));
            }

            $sorter->add($file, $deps);
        }

        return $sorter->sort();
    }
}
