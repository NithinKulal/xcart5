<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

class Refund extends ABackendAction implements IBackendAction
{
    /**
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * @var array
     */
    protected $itemsRefunded;

    /**
     * @param \XLite\Model\Order $order
     * @param array              $itemsRefunded
     */
    public function __construct(\XLite\Model\Order $order, array $itemsRefunded = [])
    {
        $this->order            = $order;
        $this->itemsRefunded    = $itemsRefunded;
    }

    /**
     * @return bool
     */
    public function isBackendApplicable()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
            && \XLite::isAdminZone()
            && $this->order;
    }

    /**
     * @return array
     */
    public function getActionDataForBackend()
    {
        $result = $this->getCommonDataForBackend();

        $result['ea']   = 'Refund action';
        $result['pa'] = 'refund';
        $result['ti'] = $this->order->getOrderNumber();

        if ($this->order->getProfile()->getGaClientId()) {
            $result['cid']  = $this->order->getProfile()->getGaClientId();
        }

        if ($this->itemsRefunded) {
            foreach ($this->itemsRefunded as $itemRefund) {
                $result += $itemRefund;
            }
        } else {
            $result['tr'] = $this->order->getTotal();
        }

        return $result;
    }
}