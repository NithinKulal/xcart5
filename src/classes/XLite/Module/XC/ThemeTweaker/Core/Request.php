<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

/**
 * Request
 */
class Request extends \XLite\Core\Request implements \XLite\Base\IDecorator
{
    /**
     * Drag-n-drop-cart feature is turned off in layout edit mode
     *
     * @return boolean
     */
    public static function isDragDropCartFlag()
    {
        return parent::isDragDropCartFlag() && !\XLite\Core\Request::getInstance()->isInLayoutMode();
    }

    /**
     * Mark templates
     *
     * @return boolean
     */
    public function isInLayoutMode()
    {
        return \XLite\Core\Config::getInstance()->XC->ThemeTweaker->layout_mode
            && !\XLite::isAdminZone()
            && \XLite\Module\XC\ThemeTweaker\Main::isTargetAllowed()
            && \XLite\Module\XC\ThemeTweaker\Main::isUserAllowed()
            && !\XLite\Core\Request::getInstance()->isPost()
            && !\XLite\Core\Request::getInstance()->isCLI()
            && !\XLite\Core\Request::getInstance()->isAJAX()
            && !\Includes\Decorator\Utils\CacheManager::isRebuildNeeded();
    }
}
