<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\View;

/**
 * Common widget extention.
 * This widget is used only to link additional css and js files to the page
 *
 * @ListChild (list="layout.main")
 */
class Common extends \XLite\View\AView
{
    /**
     * Add JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list = array_merge(
            \XLite\Module\XC\Add2CartPopup\Core\Add2CartPopup::getResourcesFiles(static::RESOURCE_JS),
            $list
        );

        $list[] = 'modules/XC/Add2CartPopup/js/add2cart_popup.js';

        return $list;
    }

    /**
     * Add CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list = array_merge(
            \XLite\Module\XC\Add2CartPopup\Core\Add2CartPopup::getResourcesFiles(static::RESOURCE_CSS),
            $list
        );

        $list[] = 'modules/XC/Add2CartPopup/css/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Add2CartPopup/common.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Module\XC\Add2CartPopup\Core\Add2CartPopup::isAdd2CartPopupEnabled();
    }
}
