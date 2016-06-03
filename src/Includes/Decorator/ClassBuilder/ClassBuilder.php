<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder;

use Includes\Reflection\ClassGenerator;
use Includes\ClassPathResolverInterface;
use Includes\Reflection\ClassTransformer;
use Includes\Reflection\ClassTransformerInterface;
use Includes\Decorator\ClassBuilder\DependencyExtractor\DependencyExtractorInterface;
use Includes\SourceToTargetPathMapperInterface;
use Includes\Reflection\StaticReflectorFactoryInterface;
use Includes\Autoload\StreamWrapperInterface;

class ClassBuilder extends AbstractClassBuilder
{
    /**
     * @var StaticReflectorFactoryInterface
     */
    private $reflectorFactory;

    /**
     * @var DependencyExtractorInterface
     */
    private $decoratorExtractor;

    /**
     * @var SourceToTargetPathMapperInterface
     */
    private $sourceToTargetPathMapper;

    private $builtClasses = [];

    private $shuttingDown = false;

    /**
     * @var ModuleRegistry
     */
    private $moduleRegistry;

    public function __construct(
        ClassPathResolverInterface $sourceClassPathResolver,
        ClassPathResolverInterface $targetClassPathResolver,
        SourceToTargetPathMapperInterface $sourceToTargetPathMapper,
        StreamWrapperInterface $streamWrapper,
        StreamWrapperInterface $decoratedAncestorStreamWrapper,
        StaticReflectorFactoryInterface $sourceStaticReflectorFactory,
        DependencyExtractor\DependencyExtractorInterface $decoratorDependencyExtractor,
        ModuleRegistry $moduleRegistry
    ) {
        parent::__construct($sourceClassPathResolver, $targetClassPathResolver, $sourceToTargetPathMapper, $streamWrapper, $decoratedAncestorStreamWrapper);

        $this->reflectorFactory         = $sourceStaticReflectorFactory;
        $this->sourceToTargetPathMapper = $sourceToTargetPathMapper;
        $this->reflectorFactory         = $sourceStaticReflectorFactory;
        $this->decoratorExtractor       = $decoratorDependencyExtractor;
        $this->moduleRegistry           = $moduleRegistry;

        register_shutdown_function(function () {
            $this->shuttingDown = true;
        });
    }

    public function buildClass($class)
    {
        if (isset($this->builtClasses[$class])) {
            return $this->builtClasses[$class];
        }

        if (!file_exists($this->sourceClassPathResolver->getPathname($class))) {
            return null;
        }

        $reflector = $this->reflectorFactory->reflectClass($class);

        if ($reflector->isClass()) {
            $this->builtClasses += $reflector->isDecorator()
                ? $this->buildDecoratedClass($reflector->getParent())
                : $this->buildDecoratedClass($class);

        } else {
            $this->copyClass($class);

            $this->builtClasses[$class] = $this->shuttingDown
                ? $this->getStream($class)
                : $this->getWrappedStream($class);
        }

        return isset($this->builtClasses[$class]) ? $this->builtClasses[$class] : null;
    }

    protected function buildDecoratedClass($class)
    {
        $module = $this->getModule($class);

        if ($module != null && !$this->moduleRegistry->has($module)) {
            return [];
        }

        if (!file_exists($this->sourceClassPathResolver->getPathname($class))) {
            return [];
        }

        $ancestor = $this->getDecoratedAncestorClassName($class);

        if (
            !$this->decoratorExtractor->areClassDecoratorsChanged($class)
            && $this->getSourceMtime($class) < $this->getTargetMtime($class)
        ) {
            $builtClasses = [];

            $decorators = $this->decoratorExtractor->getClassDecorators($class);

            if (count($decorators) > 0) {
                $builtClasses[$ancestor] = $this->shuttingDown
                    ? $this->getStream($ancestor)
                    : $this->getWrappedDecoratedAncestorStream($class);
            }

            foreach ($decorators as $pathname) {
                $decoratorClass = $this->sourceClassPathResolver->getClass($pathname);

                $builtClasses[$decoratorClass] = $this->shuttingDown
                    ? $this->getStream($decoratorClass)
                    : $this->getWrappedStream($decoratorClass);
            }

            return $builtClasses + [
                $class => count($decorators) > 0 || $this->shuttingDown
                    ? $this->getStream($class) : $this->getWrappedStream($class),
            ];
        }

        $original = $this->reflectorFactory->reflectClass($class);

        if (
            !$this->moduleRegistry->hasAll($original->getPositiveDependencies())
            || !$this->moduleRegistry->hasNone($original->getNegativeDependencies())
        ) {
            $this->writeToClass($class, '');

            return [];
        }

        $decorators = $this->decoratorExtractor->getClassDecorators($class);

        if (empty($decorators)) {
            $this->copyClass($class);

            return [$class => $this->shuttingDown ? $this->getStream($class) : $this->getWrappedStream($class)];
        }

        $generator = $this->getClassCodeGenerator($class)
            ->setAbstract(true)
            ->setClassName($this->getDecoratedAncestorClassName($original->getClassName()))
            ->removeAnnotations(['ListChild', 'AddListChild', 'ClearListChildren']); // Only @ListChild is used?

        if ($original->isEntity()) {
            $generator->removeEntityAnnotations();
        }

        $this->writeToClass($ancestor, $generator->getSource());

        $builtClasses = [];

        $builtClasses[$ancestor] = $this->shuttingDown
            ? $this->getStream($ancestor)
            : $this->getWrappedDecoratedAncestorStream($class);

        $parent = $ancestor;

        $topClass = (new ClassGenerator())
            ->setNamespace($original->getNamespace())
            ->setAbstract($original->isAbstract())
            ->setClassName($original->getClassName())
            ->setDocComment($original->getDocCommentText());

        foreach ($decorators as $pathname) {
            $source = $this->getPathnameCodeGenerator($pathname)
                ->setParent('\\' . $parent)
                ->getSource();

            $decoratorClass = $this->sourceClassPathResolver->getClass($pathname);
            $this->writeToClass($decoratorClass, $source);

            $builtClasses[$decoratorClass] = $this->shuttingDown
                ? $this->getStream($decoratorClass)
                : $this->getWrappedStream($decoratorClass);

            $decorator = $this->reflectorFactory->reflectSource($pathname);

            if (!$original->hasLifecycleCallbacks()
                && $decorator->hasLifecycleCallbacks()
                && !$decorator->isMappedSuperclass()
            ) {
                $topClass->addAnnotation('HasLifecycleCallbacks');
            }

            $parent = $decoratorClass;
        }

        $topClass->setParent($parent);

        $this->writeToClass($class, $topClass->getSource());

        return $builtClasses + [$class => $this->getStream($class)];
    }

    protected function getDecoratedAncestorClassName($original)
    {
        return $original . 'Abstract';
    }

    protected function getModule($class)
    {
        $parts = explode('\\', $class);

        return count($parts) > 3 && $parts[1] == 'Module'
            ? $parts[2] . '\\' . $parts[3]
            : null;
    }

    /**
     * @param $class
     * @return ClassTransformerInterface
     */
    protected function getClassCodeGenerator($class)
    {
        return new ClassTransformer($this->sourceClassPathResolver->getPathname($class));
    }

    /**
     * @param $pathname
     * @return ClassTransformerInterface
     */
    protected function getPathnameCodeGenerator($pathname)
    {
        return new ClassTransformer($pathname);
    }
}
