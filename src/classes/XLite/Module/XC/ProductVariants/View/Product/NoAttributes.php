<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product;

/**
 * No attributes
 *
 * @ListChild (list="admin.product.variants", zone="admin", weight="10")
 */
class NoAttributes extends \XLite\Module\XC\ProductVariants\View\Product\AProduct
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/no_attributes';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->getMultipleAttributes();
    }

    /**
     * Return block style
     *
     * @return string
     */
    protected function getBlockStyle()
    {
        return parent::getBlockStyle() . ' no-attributes';
    }
}
