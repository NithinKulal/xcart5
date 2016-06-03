<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product;

/**
 * Abstract product-base list
 */
abstract class AProduct extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Allowed sort criterions
     */
    const SORT_BY_MODE_PRICE  = 'p.price';
    const SORT_BY_MODE_NAME   = 'translations.name';
    const SORT_BY_MODE_SKU    = 'p.sku';
    const SORT_BY_MODE_AMOUNT = 'p.amount';


    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Product';
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array('products');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' products';
    }
}
