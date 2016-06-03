<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\OrderStatus;

/**
 * Payment order status selector
 */
class Payment extends \XLite\View\FormField\Select\OrderStatus\AOrderStatus
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return !\XLite\Core\Request::getInstance()->isPost() && $this->getOrder() && $this->getOrder()->getPaymentStatus()
            ? $this->getOrder()->getPaymentStatus()->getId()
            : parent::getValue();
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return '\XLite\Model\Order\Status\Payment';
    }

    /**
     * Return "all statuses" label
     *
     * @return string
     */
    protected function getAllStatusesLabel()
    {
        return 'All payment statuses';
    }
}
