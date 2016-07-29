<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model\Repo\Image\ProductVariant;

/**
 * Product varioant's image
 */
class Image extends \XLite\Model\Repo\Base\Image
{
    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'product_variant';
    }

    /**
     * Add prodct variant image to the list of storage-based repositories classes list
     *
     * @return array
     */
    protected function defineStorageRepositories()
    {
        $result = parent::defineStorageRepositories();

        $result[] = 'XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image';

        return $result;
    }

    /**
     * Count by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return integer
     */
    public function countByProduct(\XLite\Model\Product $product)
    {
        return $this->createQueryBuilder('p')
            ->linkInner('p.product_variant')
            ->andWhere('product_variant.product = :product')
            ->setParameter('product', $product)
            ->count();
    }
}
