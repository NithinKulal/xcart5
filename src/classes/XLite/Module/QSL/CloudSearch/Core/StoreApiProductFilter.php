<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Module\XC\ProductFilter\Model\Attribute;


/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ("XC\ProductFilter")
 */
class StoreApiProductFilter extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    /**
     * Check if specific attribute should be preselected as a custom filter for CloudFilters
     *
     * @param $attribute
     *
     * @return bool
     */
    protected function isPreselectAttributeAsFilter($attribute)
    {
        return $attribute['productClassId'] !== null && $attribute['visible'];
    }

    /**
     * Override to modify QueryBuilder before querying attributes
     *
     * @param $qb
     */
    protected function addProductAttributesQuerySelects($qb)
    {
        $qb->addSelect('a.visible');
    }
}
