<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * 404 controller
 */
class PageNotFound extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check whether the category title is visible in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return false;
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

        $this->headerStatus(404);
    }
}
