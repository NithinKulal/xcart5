<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager;

/**
 * Modules upload widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class UploadAddons extends \XLite\View\ModulesManager\AModulesManager
{
    /**
     * Target that is allowed for Upload Addons widget
     */
    const UPLOAD_ADDONS_TARGET = 'addon_upload';

    /**
     * Javascript file that is used for multiadd functionality
     */
    const JS_SCRIPT = 'modules_manager/upload_addons/js/upload_addons.js';


    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = self::UPLOAD_ADDONS_TARGET;

        return $result;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return void
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = self::JS_SCRIPT;

        return $list;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Upload add-on';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . LC_DS . 'upload_addons';
    }
}
