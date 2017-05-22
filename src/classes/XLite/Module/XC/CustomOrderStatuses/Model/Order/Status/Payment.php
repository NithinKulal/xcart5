<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\Model\Order\Status;

/**
 * Payment status
 *
 */
 class Payment extends \XLite\Model\Order\Status\Payment implements \XLite\Base\IDecorator
{
    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return \XLite\Model\Order\Status\Payment
     */
    public function setName($name)
    {
        $this->setCustomerName($name);

        return parent::setName($name);
    }
}