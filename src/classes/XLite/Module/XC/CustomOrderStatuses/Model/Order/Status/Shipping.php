<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\Model\Order\Status;

/**
 * Shipping status
 *
 */
 class Shipping extends \XLite\Model\Order\Status\Shipping implements \XLite\Base\IDecorator
{
    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return \XLite\Model\Order\Status\Shipping
     */
    public function setName($name)
    {
        $this->setCustomerName($name);

        return parent::setName($name);
    }
}