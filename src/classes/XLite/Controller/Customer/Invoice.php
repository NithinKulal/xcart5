<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Invoice controller
 */
class Invoice extends \XLite\Controller\Customer\Base\Order
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t(
            'Invoice #{{orderId}}, {{time}}',
            array(
                'orderId' => $this->getOrderNumber(),
                'time'    => \XLite\Core\Converter::getInstance()->formatTime($this->getOrder()->getDate())
            )
        );
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Invoice');
    }
}
