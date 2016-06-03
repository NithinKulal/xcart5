<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\FormModel\Product;

class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getDataObject()->default->identity);

        if ($product && $product->hasManualPinCodes()) {
            $schema['prices_and_inventory']['inventory_tracking']['fields']['inventory_tracking']['disabled'] = true;
            $schema['prices_and_inventory']['inventory_tracking']['fields']['quantity']['disabled'] = true;
            $schema['prices_and_inventory']['inventory_tracking']['fields']['quantity']['help'] = static::t('Quantity in stock is determined by the amount of the remaining PIN codes.');
        }

        return $schema;
    }
}
