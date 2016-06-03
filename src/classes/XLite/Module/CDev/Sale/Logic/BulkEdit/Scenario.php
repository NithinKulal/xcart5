<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Logic\BulkEdit;

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
        $result['product_price_and_membership']['fields']['default']['participate_sale'] = [
            'class'    => 'XLite\Module\CDev\Sale\Logic\BulkEdit\Field\Product\ParticipateSale',
            'options' => [
                'position' => 170,
            ],
        ];

        $result['product_price_and_membership']['fields']['default']['sale_price'] = [
            'class'    => 'XLite\Module\CDev\Sale\Logic\BulkEdit\Field\Product\SalePrice',
            'options' => [
                'position' => 171,
            ],
        ];

        return $result;
    }
}
