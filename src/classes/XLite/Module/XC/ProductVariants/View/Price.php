<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View;

/**
 * Product price
 */
class Price extends \XLite\View\Price implements \XLite\Base\IDecorator
{
    const PRICE_RANGE_DELIMITER = '–';

    const PREFIX_MIN = 'min';
    const PREFIX_MAX = 'max';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductVariants/price.twig';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getParentTemplate()
    {
        return parent::getDefaultTemplate();
    }

    /**
     * Return net price of product
     *
     * @return float
     */
    protected function getNetPrice($value = null)
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getDisplayPrice()
            : parent::getNetPrice($value);
    }

    /**
     * Return net price of product
     *
     * @return float
     */
    protected function getMinNetPrice($value = null)
    {
        $price = $this->getNetPrice($value);

        foreach ($this->getProduct()->getVariants() as $variant) {
            if ($variant->getDisplayPrice() < $price) {
                $price = $variant->getDisplayPrice();
            }
        }

        return $price;
    }

    /**
     * Return list price of product
     *
     * @return float
     */
    protected function getMinListPrice($value = null)
    {
        $id = $this->getProduct()->getProductId() . static::PREFIX_MIN;

        if (!isset(static::$listPrices[$id])) {
            $this->product->setAttrValues($this->getAttributeValues());
            static::$listPrices[$id] = $this->getMinNetPrice($value);
        }

        return static::$listPrices[$id];
    }

    /**
     * Return net price of product
     *
     * @return float
     */
    protected function getMaxNetPrice($value = null)
    {
        $price = $this->getNetPrice($value);

        foreach ($this->getProduct()->getVariants() as $variant) {
            if ($variant->getDisplayPrice() > $price) {
                $price = $variant->getDisplayPrice();
            }
        }

        return $price;
    }

    /**
     * Return list price of product
     *
     * @return float
     */
    protected function getMaxListPrice($value = null)
    {
        $id = $this->getProduct()->getProductId() . static::PREFIX_MAX;

        if (!isset(static::$listPrices[$id])) {
            $this->product->setAttrValues($this->getAttributeValues());
            static::$listPrices[$id] = $this->getMaxNetPrice($value);
        }

        return static::$listPrices[$id];
    }

    /**
     * Return delimiter for price range
     *
     * @return string
     */
    protected function getPriceRangeDelimiter()
    {
        return static::PRICE_RANGE_DELIMITER;
    }

    /**
     * Check if product price in list should be displayed as range
     *
     * @return bool
     */
    protected function isDisplayPriceAsRange()
    {
        return $this->isAllowRange() && ($this->getProduct() ? $this->getProduct()->isDisplayPriceAsRange() : false) && $this->getMaxListPrice() != $this->getMinListPrice();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = \XLite\Module\XC\ProductVariants\Main::isDisplayPriceAsRange();
        
        return $list;
    }
}
