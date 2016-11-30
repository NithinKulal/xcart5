<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

use XLite\View\CacheableTrait;

/**
 * Editable product attributes widget
 */
class EditableAttributes extends \XLite\View\Product\Details\Customer\Widget
{
    use CacheableTrait;

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-editable-attributes';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product/details/editable_attributes/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->hasAttributes();
    }

    /**
     * Check - product has visible attributes or not
     *
     * @return boolean
     */
    protected function hasAttributes()
    {
        $cacheParams   = parent::getCacheParameters();
        $cacheParams[] = $this->getProduct()->getId();
        $cacheParams[] = 'hasAttributes';

        return $this->executeCached(function () {
            return $this->getProduct()->hasEditableAttributes();
        }, $cacheParams);
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $cart      = \XLite\Model\Cart::getInstance();
        $productId = $this->getProduct()->getId();

        $list[] = $productId;
        $list[] = $cart->getItemsFingerprintByProductId($productId);

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
}
