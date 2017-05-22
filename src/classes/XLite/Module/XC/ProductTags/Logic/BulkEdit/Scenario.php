<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\Logic\BulkEdit;

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
        $result['product_categories']['title'] = \XLite\Core\Translation::getInstance()->translate('Categories and tags');

        $result['product_categories']['fields']['default']['tags'] = [
            'class'   => 'XLite\Module\XC\ProductTags\Logic\BulkEdit\Field\Product\Tag',
            'options' => [
                'position' => 200,
            ],
        ];

        return $result;
    }
}
