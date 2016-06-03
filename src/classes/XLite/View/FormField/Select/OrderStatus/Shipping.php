<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\OrderStatus;

/**
 * Shipping order status selector
 */
class Shipping extends \XLite\View\FormField\Select\OrderStatus\AOrderStatus
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return !\XLite\Core\Request::getInstance()->isPost() && $this->getOrder() && $this->getOrder()->getShippingStatus()
            ? $this->getOrder()->getShippingStatus()->getId()
            : parent::getValue();
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return '\XLite\Model\Order\Status\Shipping';
    }

    /**
     * Return "all statuses" label
     *
     * @return string
     */
    protected function getAllStatusesLabel()
    {
        return 'All shipping statuses';
    }
}
