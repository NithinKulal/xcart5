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
 * Register PHP DebugBar CSS and JS assets
 *
 * @ListChild (list="layout.main", zone="customer")
 * @ListChild (list="admin.center", zone="admin")
 */
class DebugBarAssets extends \XLite\View\AView
{
    /**
     * Display
     *
     * @param string $template Template OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        $this->initView();
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

    public function getCommonFiles()
    {
        return [
            static::RESOURCE_JS  => array_merge(
                Logic\DebugBar::getInstance()->getJsFiles(),
                [
                    ['file' => 'jstree/jstree.min.js', 'no_minify' => true],
                    'modules/XC/WebmasterKit/DebugBar/widgets.js',
                    'modules/XC/WebmasterKit/DebugBar/jquery.noconflict.js',
                ]
            ),
            static::RESOURCE_CSS => array_merge(
                Logic\DebugBar::getInstance()->getCssFiles(),
                [
                    ['file' => 'jstree/themes/default/style.min.css', 'no_minify' => true],
                    'modules/XC/WebmasterKit/DebugBar/styles.less',
                ]
            ),
        ];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return Config::getInstance()->XC->WebmasterKit->debugBarEnabled;
    }
}
