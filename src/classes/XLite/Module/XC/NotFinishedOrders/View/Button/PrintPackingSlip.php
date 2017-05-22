<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\Button;

/**
 * Test shipping rates widget
 */
class PrintPackingSlip extends \XLite\View\Button\PrintPackingSlip implements \XLite\Base\IDecorator
{
    /**
     * Return URL params to use with onclick event
     *
     * @return array
     */
    protected function getURLParams()
    {
        $result = parent::getURLParams();

        if ($this->getOrder()->isNotFinishedOrder() && isset($result['url_params'])) {
            $result['url_params']['order_id'] = $this->getOrder()->getOrderId();
            unset($result['url_params']['order_number']);
        }

        return $result;
    }
}
