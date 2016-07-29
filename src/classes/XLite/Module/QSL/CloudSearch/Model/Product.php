<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model;

use XLite\Core\Database;
use XLite\Model\Attribute;

/**
 * The "product" model class
 */
abstract class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Get all attributes
     *
     * @return array
     */
    public function getAllAttributes()
    {
        $result = array();

        foreach (Attribute::getTypes() as $type => $name) {
            $class = Attribute::getAttributeValueClass($type);
            $result = array_merge(
                $result,
                Database::getRepo($class)->findAllAttributes($this)
            );
        }

        return $result;
    }
}
