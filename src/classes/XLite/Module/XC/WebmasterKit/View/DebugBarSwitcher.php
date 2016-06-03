<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\View;

/**
 * Warning
 *
 * @ListChild (list="layout_settings.settings", zone="admin", weight="60")
 */
class DebugBarSwitcher extends \XLite\View\AView
{

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/WebmasterKit/debug_bar_switcher.twig';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/WebmasterKit/debug_bar_switch_controller.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/WebmasterKit/debug_bar_switcher.css';

        return $list;
    }

    /**
     * Change visible condition, so visible only for root admin
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && LC_DEVELOPER_MODE;
    }
}
