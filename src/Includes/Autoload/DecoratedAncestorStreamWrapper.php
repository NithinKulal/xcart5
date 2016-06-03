<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Autoload;

use Includes\SourceToTargetPathMapperInterface;

class DecoratedAncestorStreamWrapper implements StreamWrapperInterface
{
    const FILTER_ID = 'xcart.class.cache.ancestor.loader';

    public function __construct(SourceToTargetPathMapperInterface $sourceToTargetPathMapper)
    {
        if (stream_filter_register(self::FILTER_ID, 'Includes\Autoload\DecoratedAncestorStreamFilter')) {
            DecoratedAncestorStreamFilter::initialize($sourceToTargetPathMapper, $this);
        }
    }

    public function wrapStreamUri($resource)
    {
        return 'php://filter/read=' . self::FILTER_ID . '/resource=' . $resource;
    }

    public function unwrapStreamUri($wrapped)
    {
        return substr($wrapped, strlen($this->wrapStreamUri('')));
    }
}