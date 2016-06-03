<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin;

/**
 * Abstract base class for all plugins
 *
 * @package XLite
 */
abstract class APlugin extends \Includes\Decorator\ADecorator
{
    /**
     * Check - current plugin is bocking or not
     *
     * @return boolean
     */
    public function isBlockingPlugin()
    {
        return !\Includes\Decorator\Utils\CacheManager::isCapsular();
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return null;
    }
}
