<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\Button\BulkEdit;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
class Product extends \XLite\Module\XC\BulkEditing\View\Button\Product implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function getScenarios()
    {
        $result = parent::getScenarios();
        // $result['product_pin_codes'] = [
        //     'position' => 700,
        // ];

        return $result;
    }
}
