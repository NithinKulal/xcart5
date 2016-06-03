<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\OrderStatus;

/**
 * Shipping order status
 */
class Shipping extends \XLite\View\OrderStatus\AOrderStatus
{
    /**
     * Check if the widget is visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return parent::isVisible()
            && (
                \XLite::isAdminZone()
                || (
                    $this->getOrder()->getPaymentStatus()
                    && $this->getOrder()->getPaymentStatus()->isCompatibleWithShippingStatus()
                )
            );
    }

    /**
     * Return status
     *
     * @return mixed
     */
    protected function getStatus()
    {
        return $this->getOrder()
            ? $this->getOrder()->getShippingStatus()
            : null;
    }

    /**
     * Return label
     *
     * @return string
     */
    protected function getLabel()
    {
        return \XLite::isAdminZone()
            ? ''
            : static::t('Shipping status');
    }
}
