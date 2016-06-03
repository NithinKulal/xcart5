<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Autoload;

use Includes\SourceToTargetPathMapperInterface;

class DecoratedAncestorStreamFilter extends StreamFilter
{
    /** @var SourceToTargetPathMapperInterface */
    protected static $sourceToTargetPathMapper;

    /** @var StreamWrapperInterface */
    protected static $redirectingStreamFilter;

    protected function modifyStreamUri($stream)
    {
        return substr($stream, 0, -4) . 'Abstract.php';
    }
}