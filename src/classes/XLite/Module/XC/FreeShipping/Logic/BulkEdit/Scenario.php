<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Logic\BulkEdit;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
class Scenario extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected static function defineScenario()
    {
        $result = parent::defineScenario();
        $result['product_shipping_info']['fields']['default']['free_shipping'] = [
            'class'    => 'XLite\Module\XC\FreeShipping\Logic\BulkEdit\Field\Product\FreeShipping',
            'options' => [
                'position' => 210,
            ],
        ];
        $result['product_shipping_info']['fields']['default']['freight_fixed_fee'] = [
            'class'    => 'XLite\Module\XC\FreeShipping\Logic\BulkEdit\Field\Product\FreightFixedFee',
            'options' => [
                'position' => 220,
            ],
        ];

        return $result;
    }
}
