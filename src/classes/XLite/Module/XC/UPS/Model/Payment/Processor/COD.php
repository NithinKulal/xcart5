<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\Model\Payment\Processor;

/**
 * 'Cash on Delivery' payment method class
 */
class COD extends \XLite\Model\Payment\Processor\COD
{
    /**
     * Shipping method carrier code which is allowed to make COD payment method available at checkout
     *
     * @var string
     */
    protected $carrierCode = 'ups';

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }
}
