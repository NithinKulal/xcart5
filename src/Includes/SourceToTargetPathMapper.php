<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes;

class SourceToTargetPathMapper implements \Includes\SourceToTargetPathMapperInterface
{
    /**
     * @var ClassPathResolverInterface
     */
    private $sourceClassPathResolver;

    /**
     * @var ClassPathResolverInterface
     */
    private $targetClassPathResolver;

    public function __construct(
        ClassPathResolverInterface $sourceClassPathResolver,
        ClassPathResolverInterface $targetClassPathResolver
    ) {
        $this->sourceClassPathResolver = $sourceClassPathResolver;
        $this->targetClassPathResolver = $targetClassPathResolver;
    }

    public function map($source)
    {
        return $this->targetClassPathResolver->getFullPath(
            $this->sourceClassPathResolver->getRelativePath($source)
        );
    }
}