<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

use XLite\View\CacheableTrait;

/**
 * Product attributes
 */
class CommonAttributes extends \XLite\View\Product\Details\Customer\Widget
{
    use CacheableTrait;

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-common-attributes';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product/details/common_attributes/body.twig';
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
        return 0 < $this->getProduct()->getWeight()
            || 0 < strlen($this->getProduct()->getSku());
    }

    /**
     * Return SKU of product
     *
     * @return string
     */
    protected function getSKU()
    {
        return $this->getProduct()->getSKU();
    }

    /**
     * Return weight of product
     *
     * @return float
     */
    protected function getWeight()
    {
        $weight = $this->getClearWeight();

        foreach ($this->getAttributeValues() as $av) {
            if (is_object($av)) {
                $weight += $av->getAbsoluteValue('weight');
            }
        }

        return 0 < $weight ? $weight : 0;
    }

    /**
     * Get clear product weight
     *
     * @return float
     */
    protected function getClearWeight()
    {
        return $weight = $this->getProduct()->getClearWeight();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $cart = \XLite\Model\Cart::getInstance();

        $productId = $this->getProduct()->getId();

        $list[] = $productId;
        $list[] = $cart->getItemsFingerprintByProductId($productId);

        return $list;
    }
}
