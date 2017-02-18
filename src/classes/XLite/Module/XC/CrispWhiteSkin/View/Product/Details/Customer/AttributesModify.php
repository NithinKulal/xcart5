<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Product\Details\Customer;

/**
 * Product attributes modify
 */
class AttributesModify extends \XLite\View\Product\Details\Customer\AttributesModify implements \XLite\Base\IDecorator
{
    /**
     * Return specific CSS class for dialog wrapper
     *
     * @param $attribute \XLite\Model\Attribute
     *
     * @return string
     */
    protected function getAttributeCSSClass($attribute)
    {
        $class = parent::getAttributeCSSClass($attribute);

        if ($attribute->getType() == \XLite\Model\Attribute::TYPE_SELECT) {
            $class .= ' focused';
        }

        return $class;
    }
}
