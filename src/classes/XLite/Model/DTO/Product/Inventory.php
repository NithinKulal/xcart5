<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DTO\Product;

use XLite\Model\DTO\Base\CommonCell;

class Inventory extends \XLite\Model\DTO\Base\ADTO
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        $default = [
            'identity' => $object->getProductId(),

            'inventory_tracking_status'         => $object->getInventoryEnabled(),
            'quantity_in_stock'                 => $object->getAmount(),
            'low_stock_warning_on_product_page' => $object->getLowLimitEnabledCustomer(),
            'low_stock_admin_notification'      => $object->getLowLimitEnabled(),
            'low_stock_limit'                   => $object->getLowLimitAmount(),
        ];
        $this->default = new CommonCell($default);
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $default = $this->default;

        $object->setInventoryEnabled($default->inventory_tracking_status);
        $object->setAmount($default->quantity_in_stock);
        $object->setLowLimitEnabledCustomer($default->low_stock_warning_on_product_page);
        $object->setLowLimitEnabled($default->low_stock_admin_notification);
        $object->setLowLimitAmount($default->low_stock_limit);
    }
}
