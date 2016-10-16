<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\Details\Customer;

/**
 * Editable product attributes widget
 */
class EditableAttributes extends \XLite\View\Product\Details\Customer\EditableAttributes implements \XLite\Base\IDecorator
{
    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $attrs_values = [];
        foreach ($this->getAttributeValues() as $attribute) {
            $attrs_values[] = $this->getCacheParamByAttribute($attribute);
        }
        $list[] = implode(';',  $attrs_values);

        return $list;
    }

    /**
     * @param $attribute
     *
     * @return string
     */
    protected function getCacheParamByAttribute($attribute)
    {
        $attributeObj = is_array($attribute) && isset($attribute['attributeValue'])
            ? $attribute['attributeValue']
            : $attribute;

        return $attributeObj instanceof \XLite\Model\AttributeValue\AAttributeValue
            ? $attributeObj->asString()
            : md5(serialize($attributeObj));
    }
    /**
     * Prepare template display
     *
     * @param string $template Template short path
     *
     * @return array
     */
    protected function prepareTemplateDisplay($template)
    {
        $this->getProduct()->setAttrValues($this->getAttributeValues());

        return parent::prepareTemplateDisplay($template);
    }
}