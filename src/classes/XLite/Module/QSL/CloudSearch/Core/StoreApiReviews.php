<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Model\Product;


/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ("XC\Reviews")
 */
class StoreApiReviews extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    /**
     * Get sort fields that can be used to sort CloudSearch search results.
     * Sort fields are dynamic in the way that custom sort_int_*, sort_float_*, sort_str_* are allowed.
     *
     * @param Product $product
     *
     * @return array
     */
    protected function getSortFields(Product $product)
    {
        return parent::getSortFields($product) + ['sort_float_rating' => $product->getAverageRating()];
    }
}
