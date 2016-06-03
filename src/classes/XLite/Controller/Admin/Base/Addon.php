<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin\Base;

/**
 * Addon
 */
abstract class Addon extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return true if unallowed modules should be ignored on current page
     *
     * @return boolean
     */
    protected function isIgnoreUnallowedModules()
    {
        return true;
    }

    /**
     * Uninstall module action
     *
     * @param \XLite\Model\Module $module Module object to uninstall
     *
     * @return boolean
     */
    protected function uninstallModule(\XLite\Model\Module $module)
    {
        $messages = array();

        $result = \XLite\Core\Database::getRepo('XLite\Model\Module')->uninstallModule($module, $messages);

        if ($messages) {

            foreach ($messages as $message) {

                if ($result) {
                    $this->showInfo(__FUNCTION__, $message);

                } else {
                    $this->showError(__FUNCTION__, $message);
                }
            }
        }

        return $result;
    }
}
