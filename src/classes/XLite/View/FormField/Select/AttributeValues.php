<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Attribute groups selector
 */
class AttributeValues extends \XLite\View\FormField\Select\Regular
{
    /**
     * Common params
     */
    const PARAM_ATTRIBUTE  = 'attribute';
    const PARAM_PRODUCT    = 'product';

    /**
     * Get options
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();

        if (!$list) {
            $attribute = $this->getAttribute();
            $product = $this->getProduct();

            if ($attribute) {
                if ($product) {
                    foreach ($attribute->getAttributeValue($product) as $attributeValue) {
                        $list[$attributeValue->getId()] = $attributeValue->asString();
                    }

                } elseif ($attribute::TYPE_CHECKBOX == $attribute->getType()) {
                    $list[1] = static::t('Yes');
                    $list[0] = static::t('No');

                } elseif ($attribute::TYPE_SELECT == $attribute->getType()) {
                    foreach ($attribute->getAttributeOptions() as $v) {
                        $list[$v->getId()] = $v->getName();
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array();
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ATTRIBUTE => new \XLite\Model\WidgetParam\TypeObject(
                'Attribute', null, false, 'XLite\Model\Attribute'
            ),
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product', null, false, 'XLite\Model\Product'
            ),
        );
    }

    /**
     * Get attribute
     *
     * @return \XLite\Model\Attribute
     */
    public function getAttribute() {
        return $this->getParam(self::PARAM_ATTRIBUTE);
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct() {
        return $this->getParam(self::PARAM_PRODUCT);
    }
}
