<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\Details\Customer;

/**
 * Product widget
 */
abstract class Widget extends \XLite\View\Product\Details\Customer\Widget implements \XLite\Base\IDecorator
{
    /**
     * Product variant
     *
     * @var mixed
     */
    protected $productVariant;

    /**
     * Return product variant
     *
     * @return mixed
     */
    protected function getProductVariant()
    {
        if (!isset($this->productVariant)) {
            if ($this->getProduct()->mustHaveVariants()) {
                $this->productVariant = $this->getProduct()->getVariant($this->getAttributeValues());
            }

            if (!$this->productVariant) {
                $this->productVariant = false;
            }
        }

        return $this->productVariant;
    }

    /**
     * Check - 'out of stock' label is visible or not
     *
     * @return boolean
     */
    protected function isOutOfStock()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->isOutOfStock()
            : ($this->getProduct()->mustHaveVariants() ? !$this->showPlaceholderOption() : parent::isOutOfStock());
    }

    /**
     * @return boolean
     */
    public function showPlaceholderOption()
    {
        if (\XLite\Core\Config::getInstance()->General->force_choose_product_options === 'quicklook') {

            return \XLite::getController()->getTarget() !== 'product';

        } elseif (\XLite\Core\Config::getInstance()->General->force_choose_product_options === 'product_page') {

            return true;
        }

        return false;
    }

    /**
     * Check - 'out of stock' label is visible or not
     *
     * @return boolean
     */
    protected function isProductAvailableForSale()
    {
        return $this->getProductVariant()
            ? !$this->getProductVariant()->isOutOfStock()
            : ($this->getProduct()->mustHaveVariants() ? $this->showPlaceholderOption() : parent::isProductAvailableForSale());
    }
}
