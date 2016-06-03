<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product;

/**
 * Variants are based
 *
 * @ListChild (list="admin.product.variants", zone="admin", weight="20")
 */
class VariantsAreBased extends \XLite\Module\XC\ProductVariants\View\Product\AProduct
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/variants_are_based';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getVariantsAttributes();
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getTitle()
    {
        $variants = array();

        foreach ($this->getVariantsAttributes() as $v) {
            $variants[] = $v->getName();
        }

        return static::t(
            'Product variants are based on {{variants}}',
            array(
                'variants' => '<span>' . implode('</span>, <span>', $variants) . '</span>',
            )
        );
    }

    /**
     * Return block style
     *
     * @return string
     */
    protected function getBlockStyle()
    {
        return parent::getBlockStyle() . ' variants-are-based';
    }
}
