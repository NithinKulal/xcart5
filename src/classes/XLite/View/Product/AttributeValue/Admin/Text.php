<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Admin;

/**
 * Attribute value (Text)
 */
class Text extends \XLite\View\Product\AttributeValue\Admin\AAdmin
{
    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/text';
    }

    /**
     * Get attribute type
     *
     * @return string
     */
    protected function getAttributeType()
    {
        return \XLite\Model\Attribute::TYPE_TEXT;
    }

    /**
     * Check - value is editable or not
     * 
     * @return boolean
     */
    protected function isEditable()
    {
        $value = null;

        $attribute = $this->getAttribute();
        if ($attribute) {
            $value = $attribute->getAttributeValue($this->getProduct());
        }

        return $value ? $value->getEditable() : true;
    }
}
