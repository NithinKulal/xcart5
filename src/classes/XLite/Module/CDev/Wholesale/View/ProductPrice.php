<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View;

/**
 * Wholesale prices for product
 */
class ProductPrice extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Cache for wholesale prices array
     *
     * @var   array
     */
    protected $wholesalePrices = null;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Wholesale/product_price/style.css';

        return $list;
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-wholesale-prices';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Wholesale/product_price/body.twig';
    }

    /**
     * Define wholesale prices
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function defineWholesalePrices()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->getWholesalePrices(
            $this->getProduct(),
            $this->getCart()->getProfile() ? $this->getCart()->getProfile()->getMembership() : null
        );
    }

    /**
     * Return wholesale prices for the current product
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function getWholesalePrices()
    {
        if (!isset($this->wholesalePrices)) {
            $this->wholesalePrices = $this->defineWholesalePrices();

            $minQty = $this->getProduct()->getMinQuantity($this->getCart()->getProfile() ? $this->getCart()->getProfile()->getMembership() : null);
            if (
                $this->wholesalePrices
                && isset($this->wholesalePrices[0])
                && $minQty < $this->wholesalePrices[0]->getQuantityRangeBegin()
            ) {
                $class = get_class($this->wholesalePrices[0]);
                $basePrice = new $class;
                $basePrice->setPrice($this->wholesalePrices[0]->getOwner()->getBasePrice());
                $basePrice->setQuantityRangeBegin($minQty);
                $basePrice->setQuantityRangeEnd($this->wholesalePrices[0]->getQuantityRangeBegin() - 1);
                $basePrice->setOwner($this->wholesalePrices[0]->getOwner());
                array_unshift($this->wholesalePrices, $basePrice);
            }
        }

        return $this->wholesalePrices;
    }
}
