<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\View;

use XLite\Core\Config;
use \XLite\Module\XC\WebmasterKit\Logic;

/**
 * PHP DebugBar renderer
 *
 * @ListChild (list="jscontainer.js", zone="customer", weight="1000")
 * @ListChild (list="jscontainer.js", zone="admin", weight="1000")
 */
class DebugBar extends \XLite\View\AView
{
    // This placeholder is used for deferred rendering of DebugBar.
    // It is replaced with the actual DebugBar code after all widgets have finished their rendering.
    const CONTENT_PLACEHOLDER = '__DEBUG_BAR_CONTENT_PLACEHOLDER__';

    /**
     * Display
     *
     * @param string $template Tempalte OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        echo self::CONTENT_PLACEHOLDER;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return Config::getInstance()->XC->WebmasterKit->debugBarEnabled
            && \XLite\Module\XC\WebmasterKit\Core\Profiler::getInstance()->getStartupFlag();
    }
}
