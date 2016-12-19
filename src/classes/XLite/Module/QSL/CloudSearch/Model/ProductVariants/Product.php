<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\ProductVariants;

use XLite\Core\Database;


/**
 * The "product" model class
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
abstract class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    protected $constrainCloudSearchProductVariants;

    /**
     * Define default variant
     *
     * @return \XLite\Module\XC\ProductVariants\Model\ProductVariant
     */
    protected function defineDefaultVariant()
    {
        $defVariant = null;

        if ($this->constrainCloudSearchProductVariants !== null) {
            if ($this->mustHaveVariants() && $this->hasVariants()) {
                $filteredVariants = $this->getFilteredCloudSearchVariants();

                $repo = Database::getRepo('\XLite\Module\XC\ProductVariants\Model\ProductVariant');
                $defVariant = $repo->findOneBy(
                    array(
                        'product'      => $this,
                        'defaultValue' => true,
                    )
                );

                if (
                    !$defVariant
                    || $defVariant->isOutOfStock()
                    || !$filteredVariants->contains($defVariant)
                ) {
                    $minPrice             = $minPriceOutOfStock = false;
                    $defVariantOutOfStock = null;
                    foreach ($filteredVariants as $variant) {
                        if (!$variant->isOutOfStock()) {
                            if (false === $minPrice || $minPrice > $variant->getClearPrice()) {
                                $minPrice   = $variant->getClearPrice();
                                $defVariant = $variant;
                            }
                        } elseif (!$defVariant) {
                            if (false === $minPriceOutOfStock || $minPriceOutOfStock > $variant->getClearPrice()) {
                                $minPriceOutOfStock   = $variant->getClearPrice();
                                $defVariantOutOfStock = $variant;
                            }
                        }
                    }
                    $defVariant = $defVariant ?: $defVariantOutOfStock;
                }
            }
        }

        return $defVariant !== null ? $defVariant : parent::defineDefaultVariant();
    }

    /**
     * Get variants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilteredCloudSearchVariants()
    {
        $variants = parent::getVariants();

        $variantIds = array_map(function ($v) {
            return $v['id'];
        }, $this->constrainCloudSearchProductVariants);

        $variants = $variants->filter(function ($v) use ($variantIds) {
            return in_array($v->getId(), $variantIds);
        });

        return $variants;
    }

    /**
     * Constrain product variants so that only filtered could be shown on a product list
     *
     * @param $filterVariants
     */
    public function constrainCloudSearchProductVariants($filterVariants)
    {
        $this->constrainCloudSearchProductVariants = $filterVariants;
    }

    /**
     * Remove filter constraint set with the above method
     */
    public function unconstrainCloudSearchProductVariants()
    {
        $this->constrainCloudSearchProductVariants = null;
    }
}
