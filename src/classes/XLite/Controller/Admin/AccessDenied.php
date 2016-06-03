<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Access denied page controller
 */
class AccessDenied extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return true;
    }

    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        parent::handleRequest();

        $this->headerStatus(403);
    }
}
