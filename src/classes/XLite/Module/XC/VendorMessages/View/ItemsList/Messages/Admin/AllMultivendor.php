<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Admin;

/**
 * All admin messages
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class AllMultivendor extends \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Admin\All implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $condition = parent::getSearchCondition();
        if (\XLite\Module\XC\VendorMessages\Main::isVendorAllowed() && \XLite\Core\Auth::getInstance()->isVendor()) {
            $condition->{\XLite\Model\Repo\Order::P_VENDOR_ID} = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        }

        return $condition;
    }

    /**
     * @inheritdoc
     */
    protected function isThreadsMultiple(\XLite\Model\Order $order)
    {
        return \XLite\Module\XC\VendorMessages\Main::isWarehouse()
            && count($order->getChildren()) > 0;
    }


}
