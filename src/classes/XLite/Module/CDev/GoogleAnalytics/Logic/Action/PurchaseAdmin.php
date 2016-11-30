<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderDataMapper;

/**
 * Class PurchaseAdmin
 */
class PurchaseAdmin extends ABackendAction implements IBackendAction
{
    /**
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * @var array
     */
    protected $itemsPurchased;

    /**
     * @param \XLite\Model\Order $order
     * @param array              $itemsPurchased
     */
    public function __construct(\XLite\Model\Order $order, $itemsPurchased)
    {
        $this->order            = $order;
        $this->itemsPurchased   = $itemsPurchased;
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
        
        if ($this->order->getProfile()->getGaClientId()) {
            $result['cid']  = $this->order->getProfile()->getGaClientId();
        }
        $result['ea'] = 'Purchase action';
        $result['pa'] = 'purchase';

        if ($this->itemsPurchased) {
            foreach ($this->itemsPurchased as $itemPurchase) {
                $result += $itemPurchase;
            }
        }

        $purchaseData = OrderDataMapper::getPurchaseDataForBackend($this->order);
        $purchaseData['tr'] = '0';
        $purchaseData['tt'] = '0';
        $purchaseData['ts'] = '0';

        $result += $purchaseData;

        return $result;
    }
}