<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2017-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/module-marketplace-terms-of-use.html for license details.
 */

namespace XLite\Module\QSL\SpecialOffersBase\Logic\Order\Modifier;

/**
 * Discount for selected payment method.
 *
 * @Decorator\Depend ("XC\MultiVendor")
 */
class SpecialOffersWithVendors extends \XLite\Module\QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers
    implements \XLite\Base\IDecorator
{
    /**
     * Calculate.
     * 
     * Calculate for end orders only (parent for warehouse mode and children
     * for non warehouse mode).
     * 
     * @return void
     */
    public function calculate()
    {
        $order = $this->getOrder();
        $warehouseMode = \XLite\Module\XC\MultiVendor\Main::isWarehouseMode();

        return !($order instanceof \XLite\Model\Cart)
            || (($order->isChild() && !$warehouseMode) || ($order->isParent() && $warehouseMode))
                ? parent::calculate()
                : null;
    }
}