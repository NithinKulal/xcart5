<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

/**
 * Product widget
 */
abstract class Widget extends \XLite\View\Product\AProduct
{
    /**
     * Widget parameters
     */
    const PARAM_PRODUCT          = 'product';
    const PARAM_PRODUCT_ID       = 'product_id';
    const PARAM_ATTRIBUTE_VALUES = 'attribute_values';

    /**
     * Product model cache
     *
     * @var \XLite\Model\Product | null
     */
    protected $product;

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    abstract public function getFingerprint();

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PRODUCT          => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
            static::PARAM_PRODUCT_ID       => new \XLite\Model\WidgetParam\TypeInt('Product ID', 0),
            static::PARAM_ATTRIBUTE_VALUES => new \XLite\Model\WidgetParam\TypeString('Attribute values IDs', ''),
        );
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        if (is_null($this->product)) {
            $this->product = $this->getParam(self::PARAM_PRODUCT_ID)
                ? \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getParam(self::PARAM_PRODUCT_ID))
                : $this->getParam(self::PARAM_PRODUCT);

            $this->product->setAttrValues($this->getAttributeValues());
        }

        return $this->product;
    }

    /**
     * Return product attributes array from the request parameters
     *
     * @return array
     */
    protected function getAttributeValues()
    {
        $ids = array();
        $attributeValues = trim($this->getParam(static::PARAM_ATTRIBUTE_VALUES), ',');

        if ($attributeValues) {
            $attributeValues = explode(',', $attributeValues);
            foreach ($attributeValues as $v) {
                $v = explode('_', $v);
                $ids[$v[0]] = $v[1];
            }
        }

        return $this->getProduct()->prepareAttributeValues($ids);
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getProduct();
    }
}
