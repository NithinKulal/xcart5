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
     * @param mixed|\XLite\Model\Product $data
     */
    protected function init($data)
    {
        $default = [
            'identity' => $data->getProductId(),

            'inventory_tracking_status'         => $data->getInventoryEnabled(),
            'quantity_in_stock'                 => $data->getAmount(),
            'low_stock_warning_on_product_page' => $data->getLowLimitEnabledCustomer(),
            'low_stock_admin_notification'      => $data->getLowLimitEnabled(),
            'low_stock_limit'                   => $data->getLowLimitAmount(),
        ];
        $this->default = new CommonCell($default);
    }

    /**
     * @param \XLite\Model\Product $dataObject
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($dataObject, $rawData = null)
    {
        $default = $this->default;

        $dataObject->setInventoryEnabled($default->inventory_tracking_status);
        $dataObject->setAmount($default->quantity_in_stock);
        $dataObject->setLowLimitEnabledCustomer($default->low_stock_warning_on_product_page);
        $dataObject->setLowLimitEnabled($default->low_stock_admin_notification);
        $dataObject->setLowLimitAmount($default->low_stock_limit);
    }
}
