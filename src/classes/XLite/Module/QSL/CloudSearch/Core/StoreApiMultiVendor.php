<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Model\Product;
use XLite\Module\XC\MultiVendor\Model\Product as MultiVendorProduct;


/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"XC\MultiVendor"})
 */
class StoreApiMultiVendor extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    /**
     * Get product attributes data
     *
     * @param $product
     *
     * @return array
     */
    protected function getProductAttributes(Product $product)
    {
        $attributes = parent::getProductAttributes($product);

        /** @var MultiVendorProduct $product */
        $vendor = $product->getVendor();

        $vendorName = $vendor !== null ? $vendor->getVendorCompanyName() : $this->t('Main vendor');

        if (!empty($vendorName)) {
            $attributes[] = [
                'id'                => 'XC\MultiVendor',
                'name'              => $this->t('Vendor'),
                'preselectAsFilter' => true,
                'group'             => 'Multi-vendor module',
                'values'            => [$vendorName],
            ];
        }

        return $attributes;
    }
}
