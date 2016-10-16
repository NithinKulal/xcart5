<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder;

use Includes\ClassPathResolverInterface;
use Includes\Utils\FileManager;
use Includes\SourceToTargetPathMapperInterface;
use Includes\Autoload\StreamWrapperInterface;

abstract class AbstractClassBuilder implements ClassBuilderInterface
{
    /**
     * @var ClassPathResolverInterface
     */
    protected $sourceClassPathResolver;

    /**
     * @var ClassPathResolverInterface
     */
    protected $targetClassPathResolver;

    /**
     * @var SourceToTargetPathMapperInterface
     */
    private $sourceToTargetPathMapper;

    /**
     * @var StreamWrapperInterface
     */
    private $streamWrapper;

    /**
     * @var StreamWrapperInterface
     */
    private $decoratedAncestorStreamWrapper;

    public function __construct(
        ClassPathResolverInterface $sourceClassPathResolver,
        ClassPathResolverInterface $targetClassPathResolver,
        SourceToTargetPathMapperInterface $sourceToTargetPathMapper,
        StreamWrapperInterface $streamWrapper,
        StreamWrapperInterface $decoratedAncestorStreamWrapper
    ) {
        $this->sourceClassPathResolver        = $sourceClassPathResolver;
        $this->targetClassPathResolver        = $targetClassPathResolver;
        $this->sourceToTargetPathMapper       = $sourceToTargetPathMapper;
        $this->streamWrapper                  = $streamWrapper;
        $this->decoratedAncestorStreamWrapper = $decoratedAncestorStreamWrapper;
    }

    protected function copyClass($class)
    {
        $sourcePathname = $this->sourceClassPathResolver->getPathname($class);
        $targetPathname = $this->targetClassPathResolver->getPathname($class);

        if (!is_dir(dirname($targetPathname))) {
            FileManager::mkdirRecursive(dirname($targetPathname));
        }

        if ($this->getSourceMtime($class) >= $this->getTargetMtime($class)) {
            copy($sourcePathname, $targetPathname);
        }
    }

    protected function writeToClass($class, $source)
    {
        $targetPathname = $this->targetClassPathResolver->getPathname($class);

        if (!is_dir(dirname($targetPathname))) {
            FileManager::mkdirRecursive(dirname($targetPathname));
        }

        file_put_contents($targetPathname, $source);
    }

    protected function getStream($class)
    {
        return $this->targetClassPathResolver->getPathname($class);
    }

    protected function getWrappedStream($class)
    {
        $uri = $this->sourceClassPathResolver->getPathname($class);

        return $this->streamWrapper->wrapStreamUri($uri);
    }

    protected function getWrappedDecoratedAncestorStream($class)
    {
        $uri = $this->sourceClassPathResolver->getPathname($class);

        return $this->decoratedAncestorStreamWrapper->wrapStreamUri($uri);
    }

    protected function getSourceMtime($class)
    {
        return filemtime($this->sourceClassPathResolver->getPathname($class));
    }

    protected function getTargetMtime($class)
    {
        $target = $this->targetClassPathResolver->getPathname($class);

        return file_exists($target) ? filemtime($target) : null;
    }
}
