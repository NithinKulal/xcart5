<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;

use Includes\Annotations\Parser\AnnotationParserFactory;
use Includes\ClassPathResolverInterface;
use Includes\Annotations\Parser\AnnotationParserInterface;
use Includes\SourceToTargetPathMapperInterface;

class StaticReflectorFactory implements StaticReflectorFactoryInterface
{
    /** @var AnnotationParserInterface */
    private $annotationParser;

    /** @var ClassPathResolverInterface */
    private $classPathResolver;

    /**
     * @var SourceToTargetPathMapperInterface
     */

    private $reflectors = [];

    public function __construct(ClassPathResolverInterface $classPathResolver)
    {
        $this->annotationParser  = (new AnnotationParserFactory())->create();
        $this->classPathResolver = $classPathResolver;
    }

    /**
     * @param $class
     * @return StaticReflectorInterface
     */
    public function reflectClass($class)
    {
        $pathname = $this->classPathResolver->getPathname($class);

        return $this->reflectSource($pathname);
    }

    /**
     * @param $pathname
     * @return StaticReflectorInterface
     */
    public function reflectSource($pathname)
    {
        if (!isset($this->reflectors[$pathname])) {
            $this->reflectors[$pathname] = new CachedStaticReflector(
                $this->classPathResolver,
                $this->annotationParser,
                $pathname
            );
        }

        return $this->reflectors[$pathname];
    }
}