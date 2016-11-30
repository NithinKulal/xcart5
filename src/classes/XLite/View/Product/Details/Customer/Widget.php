<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Product widget
 */
abstract class Widget extends \XLite\View\Product\AProduct
{
    use ExecuteCachedTrait;

    /**
     * Widget parameters
     */
    const PARAM_PRODUCT          = 'product';
    const PARAM_PRODUCT_ID       = 'product_id';
    const PARAM_ATTRIBUTE_VALUES = 'attribute_values';

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

        $this->widgetParams += [
            static::PARAM_PRODUCT          => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, 'XLite\Model\Product'),
            static::PARAM_PRODUCT_ID       => new \XLite\Model\WidgetParam\TypeInt('Product ID', 0),
            static::PARAM_ATTRIBUTE_VALUES => new \XLite\Model\WidgetParam\TypeString('Attribute values IDs', $this->getDefaultAttributeValues()),
        ];
    }

    /**
     * @return array
     */
    protected function getDefaultAttributeValues()
    {
        return is_string(\XLite\Core\Request::getInstance()->attribute_values)
            ? \XLite\Core\Request::getInstance()->attribute_values
            : '';   
    }
    
    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        $product = $this->getRuntimeCache('getProduct');
        if (!$product) {
            $product = $this->executeCachedRuntime(function () {
                $productId = $this->getParam(self::PARAM_PRODUCT_ID);
                /** @var \XLite\Model\Product $product */
                $product = $productId
                    ? \XLite\Core\Database::getRepo('XLite\Model\Product')->find($productId)
                    : $this->getParam(self::PARAM_PRODUCT);

                return $product;
            });

            $product->setAttrValues($this->getAttributeValues());
        }

        return $product;
    }

    /**
     * Return product attributes array from the request parameters
     *
     * @return array
     */
    protected function getAttributeValues()
    {
        return $this->executeCachedRuntime(function () {
            $result          = [];
            $attributeValues = trim($this->getParam(static::PARAM_ATTRIBUTE_VALUES), ',');

            if ($attributeValues) {
                $attributeValues = explode(',', $attributeValues);
                foreach ($attributeValues as $attributeValue) {
                    list($attributeId, $valueId) = explode('_', $attributeValue);

                    $result[$attributeId] = $valueId;
                }
            }

            return $this->getProduct()->prepareAttributeValues($result);
        });
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
