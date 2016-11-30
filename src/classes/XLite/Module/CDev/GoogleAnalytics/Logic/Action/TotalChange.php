<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderDataMapper;

class TotalChange extends ABackendAction implements IBackendAction
{
    /**
     * @var \XLite\Model\Order
     */
    protected $order;
    /**
     * @var array
     */
    private $orderChanges;

    /**
     * @param \XLite\Model\Order $order
     * @param array              $orderChanges
     */
    public function __construct(\XLite\Model\Order $order, array $orderChanges = [])
    {
        $this->order = $order;
        $this->orderChanges = $orderChanges;
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

        $result['pa'] = 'purchase';
        $result['ea'] = 'Total change action';
        $result['ti'] = $this->order->getOrderNumber();

        if ($this->order->getProfile()->getGaClientId()) {
            $result['cid']  = $this->order->getProfile()->getGaClientId();
        }

        $result += OrderDataMapper::getChangeDataForBackend($this->order, $this->orderChanges);

        return $result;
    }
}