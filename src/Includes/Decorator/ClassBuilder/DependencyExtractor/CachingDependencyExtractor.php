<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder\DependencyExtractor;

use Includes\ClassPathResolverInterface;
use Includes\Utils\FileManager;

class CachingDependencyExtractor implements DependencyExtractorInterface
{
    /**
     * @var DependencyExtractorInterface
     */
    private $decoratorExtractor;

    /**
     * @var ClassPathResolverInterface
     */
    private $targetClassPathResolver;

    private $decoratorCandidatesMaxMtime;

    private $cachedClassDecorators;

    private $flippedDecoratorCandidates;

    public function __construct(
        DependencyExtractorInterface $decoratorExtractor,
        ClassPathResolverInterface $targetClassPathResolver
    ) {
        $this->decoratorExtractor      = $decoratorExtractor;
        $this->targetClassPathResolver = $targetClassPathResolver;
    }

    public function getDecoratorCandidates()
    {
        return $this->decoratorExtractor->getDecoratorCandidates();
    }

    public function getDecorators()
    {
        return $this->decoratorExtractor->getDecorators();
    }

    public function getClassDecoratorCandidates($class)
    {
        return $this->decoratorExtractor->getClassDecoratorCandidates($class);
    }

    public function getClassDecorators($class)
    {
        if (($cached = $this->getCachedClassDecorators($class)) !== null) {
            return $cached;
        }

        $classDecorators = $this->decoratorExtractor->getClassDecorators($class);

        $this->putDecoratedClassMetadata($class, new DecoratedClassMetadata($classDecorators));

        return $classDecorators;
    }

    public function areClassDecoratorsChanged($class)
    {
        return $this->getCachedClassDecorators($class) === null;
    }

    private function getCachedClassDecorators($class)
    {
        if (!isset($this->cachedClassDecorators[$class])) {
            $metadataMtime = $this->getDecoratedClassMetadataMtime($class);

            if (
                (($decoratorCandidatesUnchanged = ($this->getDecoratorCandidatesMtime() < $metadataMtime))
                 || $this->getClassDecoratorCandidatesMtime($class) < $metadataMtime)
                && ($meta = $this->getDecoratedClassMetadata($class)) != null
                && (!$meta->getDecorators() || $this->allDecoratorsExist($meta->getDecorators()))
            ) {
                if (!$decoratorCandidatesUnchanged) {
                    $this->touchDecoratedClassMetadata($class);
                }

                $this->cachedClassDecorators[$class] = $meta->getDecorators();
            } else {
                $this->cachedClassDecorators[$class] = false;
            }
        }

        return $this->cachedClassDecorators[$class] !== false ? $this->cachedClassDecorators[$class] : null;
    }

    private function allDecoratorsExist($decorators)
    {
        if (!isset($this->flippedDecoratorCandidates)) {
            $this->flippedDecoratorCandidates = array_flip($this->decoratorExtractor->getDecoratorCandidates());
        }

        foreach ($decorators as $pathname) {
            if (!isset($this->flippedDecoratorCandidates[$pathname])) {
                return false;
            }
        }

        return true;
    }

    private function getClassDecoratorCandidatesMtime($class)
    {
        $mtimes = array_map('filemtime', $this->getClassDecoratorCandidates($class));

        return !empty($mtimes) ? max($mtimes) : null;
    }

    private function getDecoratorCandidatesMtime()
    {
        if (!isset($this->decoratorCandidatesMaxMtime)) {
            $mtimes = array_map('filemtime', $this->getDecoratorCandidates());

            $this->decoratorCandidatesMaxMtime = !empty($mtimes) ? max($mtimes) : 0;
        }

        return $this->decoratorCandidatesMaxMtime;
    }

    /**
     * @param $class
     * @return DecoratedClassMetadata
     */
    private function getDecoratedClassMetadata($class)
    {
        $metadataFilename = $this->getMetadataPathname($class);

        if (file_exists($metadataFilename)) {
            return unserialize(file_get_contents($metadataFilename));
        }

        return null;
    }

    private function getDecoratedClassMetadataMtime($class)
    {
        $metadataFilename = $this->getMetadataPathname($class);

        return file_exists($metadataFilename) ? filemtime($metadataFilename) : null;
    }

    private function putDecoratedClassMetadata($class, DecoratedClassMetadata $decoratedClassMetadata)
    {
        $metadataFilename = $this->getMetadataPathname($class);

        if (!is_dir(dirname($metadataFilename))) {
            FileManager::mkdirRecursive(dirname($metadataFilename));
        }

        file_put_contents($metadataFilename, serialize($decoratedClassMetadata));
    }

    private function touchDecoratedClassMetadata($class)
    {
        $metadataFilename = $this->getMetadataPathname($class);

        touch($metadataFilename);
    }

    /**
     * @param $class
     * @return string
     */
    private function getMetadataPathname($class)
    {
        return $this->targetClassPathResolver->getPathname($class) . '.decorators';
    }
}