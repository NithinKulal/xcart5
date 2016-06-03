<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Back from payment controller
 */
class BackFromPayment extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->isAJAX();
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        \XLite\Core\Session::getInstance()->checkoutCanceled = false;
    }
}
