<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Addon;

/**
 * Upload addon button
 */
class Upload extends \XLite\View\Button\APopupButton
{
    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = \XLite\View\ModulesManager\UploadAddons::JS_SCRIPT;

        return $list;
    }

    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Upload add-on';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target' => \XLite\View\ModulesManager\UploadAddons::UPLOAD_ADDONS_TARGET,
            'widget' => '\XLite\View\ModulesManager\UploadAddons',
        );
    }

    /**
     * getClass
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' upload-addons';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && 'addons_list_installed' === \XLite\Core\Request::getInstance()->target
            && !isset(\XLite\Core\Request::getInstance()->recent);
    }
}
