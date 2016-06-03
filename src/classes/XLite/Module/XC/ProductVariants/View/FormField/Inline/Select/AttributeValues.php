<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Inline\Select;

/**
 * Attribute values selector
 */
class AttributeValues extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Select\AttributeValues';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-attribute-value';
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }

    /**
     * Save value
     *
     * @return void
     */
    public function saveValue()
    {
        $variant = $this->getEntity();
        $attribute = $this->getSingleFieldAsWidget()->getAttribute();
        $attributeValue = \XLite\Core\Database::getRepo(
            $attribute->getAttributeValueClass(
                $attribute->getType()
            )
        )->find($this->getSingleFieldAsWidget()->getValue());

        if ($attributeValue) {
            $method = 'addAttributeValue' . $attribute->getType();
            $variant->$method($attributeValue);
            $attributeValue->addVariants($variant);
        }
    }
}
