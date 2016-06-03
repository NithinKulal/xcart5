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
//        private $sourceToTargetPathMapper;

    private $reflectors = [];

    public function __construct(
        ClassPathResolverInterface $classPathResolver/*,
            SourceToTargetPathMapperInterface $sourceToTargetPathMapperInterface*/
    )
    {
        $this->annotationParser  = (new AnnotationParserFactory())->create();
        $this->classPathResolver = $classPathResolver;
//            $this->sourceToTargetPathMapper = $sourceToTargetPathMapperInterface;
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
                $this->annotationParser,
                $pathname
            );

            /*$metadataFile = $this->getMetadataPathname($sourcePathname);

            if (file_exists($metadataFile) && filemtime($sourcePathname) < filemtime($metadataFile)) {
                $reflector->unserializeState(file_get_contents($metadataFile));
            }*/
        }

        return $this->reflectors[$pathname];
    }

    /*public function finalizeReflector(StaticReflectorInterface $reflector)
    {
        // Do not write if state was not changed

        if ($reflector instanceof SerializableState) {
            file_put_contents(
                $this->getMetadataPathname($reflector->getPathname()),
                $reflector->serializeState()
            );
        }
    }

    private function getMetadataPathname($sourcePathname)
    {
        return $this->sourceToTargetPathMapper->map($sourcePathname) . '.metadata';
    }*/
}