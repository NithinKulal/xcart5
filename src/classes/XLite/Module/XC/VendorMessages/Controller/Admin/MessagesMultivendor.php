<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Controller\Admin;

/**
 * Messages
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class MessagesMultivendor extends \XLite\Module\XC\VendorMessages\Controller\Admin\Messages implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && (!\XLite\Core\Auth::getInstance()->isVendor() || \XLite\Module\XC\VendorMessages\Main::isVendorAllowed());
    }

    /**
     * @inheritdoc
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('[vendor] manage orders');
    }

    /**
     * @inheritdoc
     */
    public function isSearchVisible()
    {
        return (parent::isSearchVisible() && !\XLite\Core\Auth::getInstance()->isVendor())
            || (\XLite\Core\Auth::getInstance()->isVendor() && \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->countByVendor() > 0);
    }

    /**
     * Changes conversations subscriptions
     */
    protected function doActionChangeSubscriptions()
    {
        if (
            !\XLite\Module\XC\VendorMessages\Main::isWarehouse()
            && !\XLite\Core\Auth::getInstance()->isVendor()
        ) {
            $list = \XLite\Core\Request::getInstance()->subscribe;
            if ($list && is_array($list)) {
                foreach ($list as $orderid => $mark) {
                    $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderid);
                    if ($order) {
                        $order->setIsWatchMessages((bool)$mark);
                    }
                }
                \XLite\Core\Database::getEM()->flush();
                \XLite\Core\Event::orderMessagesWatch();
                \XLite\Core\TopMessage::addInfo(
                    'Monitoring of communication related to oder #X has been enabled',
                    array('order_number' => $this->getOrder()->getOrderNumber())
                );
            }
        }

        $this->restoreFormId();
    }
}
