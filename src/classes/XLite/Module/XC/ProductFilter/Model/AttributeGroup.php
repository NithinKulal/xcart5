<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Model;

/**
 * AttributeGroup
 *
 */
class AttributeGroup extends \XLite\Model\AttributeGroup implements \XLite\Base\IDecorator
{
    /**
     * Check if product class has non-empty atributes
     * 
     * @return boolean
     */
    public function hasNonEmptyAttributes()
    {
        $result = false;

        foreach ($this->getAttributes() as $attribute) {
            if (
                $attribute::TYPE_SELECT != $attribute->getType()
                || 0 < count($attribute->getAttributeOptions())
            ) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}