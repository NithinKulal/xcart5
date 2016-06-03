<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\FormModel\Product;

class Inventory extends \XLite\View\FormModel\Product\Inventory implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getDataObject()->default->identity);

        if ($product && $product->hasManualPinCodes()) {
            $schema[self::SECTION_DEFAULT]['inventory_tracking_status']['disabled'] = true;
            $schema[self::SECTION_DEFAULT]['quantity_in_stock']['disabled'] = true;
            $schema[self::SECTION_DEFAULT]['quantity_in_stock']['help'] = static::t('Quantity in stock is determined by the amount of the remaining PIN codes.');
        }

        return $schema;
    }
}
