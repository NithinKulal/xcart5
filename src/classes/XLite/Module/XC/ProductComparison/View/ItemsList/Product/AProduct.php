<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductComparison\View\ItemsList\Product;

/**
 * Abstract product list
 */
abstract class AProduct extends \XLite\View\ItemsList\Product\AProduct implements \XLite\Base\IDecorator
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ProductComparison/compare/script.js';
        $list[] = 'modules/XC/ProductComparison/compare/products/script.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ProductComparison/compare/style.css';
        $list[] = 'modules/XC/ProductComparison/compare/products/style.css';

        return $list;
    }
}
