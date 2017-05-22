<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base;

/**
 * Order messages
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
abstract class AllMultivendor extends \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base\All implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function getLastMessage(\XLite\Model\Order $order)
    {
        if (\XLite\Module\XC\VendorMessages\Main::isWarehouse()) {
            $result = null;

            if (\XLite\Core\Auth::getInstance()->isVendor()) {
                foreach ($order->getChildren() as $o) {
                    if ($o->getVendor() && $o->getVendor()->getProfileId() == \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()) {
                        $result = $o->getLastMessage();
                        break;
                    }
                }

            } else {
                $max_date = null;
                if ($order->getLastMessage()) {
                    $result = $order->getLastMessage();
                    $max_date = $result->getDate();
                }
                foreach ($order->getChildren() as $o) {
                    if ($o->getLastMessage() && (!isset($max_date) || $max_date < $o->getLastMessage()->getDate())) {
                        $result = $o->getLastMessage();
                        $max_date = $result->getDate();
                    }
                }
            }

        } else {
            $result = parent::getLastMessage($order);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function isMarksVisible(\XLite\Model\Order $order)
    {
        return \XLite\Module\XC\VendorMessages\Main::isAllowDisputes()
            && ($this->isOpenedDispute($order) || $this->isWatchVisible($order));
    }

    /**
     * Check - order has opened dispute or not
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function isOpenedDispute(\XLite\Model\Order $order)
    {
        $result = $order->getIsOpenedDispute();
        if (\XLite\Module\XC\VendorMessages\Main::isWarehouse() && !$result) {
            foreach ($order->getChildren() as $o) {
                $result = $o->getIsOpenedDispute();
                if ($result) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Watch switcher is visible or not
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function isWatchVisible(\XLite\Model\Order $order)
    {
        return \XLite\Core\Auth::getInstance()->isAdmin() && !\XLite\Core\Auth::getInstance()->isVendor();
    }

    /**
     * Check - user watch order's messages or not
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function isWatchMessages(\XLite\Model\Order $order)
    {
        $result = $order->getIsWatchMessages();
        if (\XLite\Module\XC\VendorMessages\Main::isWarehouse() && !$result) {
            foreach ($order->getChildren() as $o) {
                $result = $o->getIsWatchMessages();
                if ($result) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getLineTagAttributes(\XLite\Model\Order $order)
    {
        $attributes = parent::getLineTagAttributes($order);

        if ($this->isMarksVisible($order)) {
            $attributes['class'][] = 'has-marks';
        }

        return $attributes;
    }


}
