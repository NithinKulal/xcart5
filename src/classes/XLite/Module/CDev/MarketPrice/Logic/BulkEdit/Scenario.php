<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\MarketPrice\Logic\BulkEdit;

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
        $result['product_price_and_membership']['fields']['default']['market_price'] = [
            'class'    => 'XLite\Module\CDev\MarketPrice\Logic\BulkEdit\Field\Product\MarketPrice',
            'options' => [
                'position' => 150,
            ],
        ];

        return $result;
    }
}
