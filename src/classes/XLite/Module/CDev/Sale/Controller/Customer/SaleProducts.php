<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Controller\Customer;

/**
 * Sale products page controller
 */
class SaleProducts extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Sale');
    }

    /**
     * Common method to determine current location
     *
     * @return array
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }
}
