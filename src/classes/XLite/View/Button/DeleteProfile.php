<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;


/**
 * Delete profile button. Admin area.
 */
class DeleteProfile extends \XLite\View\Button\Regular
{

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'delete';
    }

    /**
     * Return specified JS code
     *
     * @return string
     */
    protected function getJSCode()
    {
        // We got the default JS code.
        $jsCode = $this->getDefaultJSCode();

        // Message to show admin user. the profile will be removed.
        $message = static::t('Are you sure you want to delete the selected user?');

        // We show confirmation message and remove user profile after admin confirmation only
        return 'if(confirm(\'' . $message . '\')){' . $jsCode . '}';
    }
}
