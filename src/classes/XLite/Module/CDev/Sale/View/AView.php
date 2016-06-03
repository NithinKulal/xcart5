<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Viewer
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Sale/css/lc.css';

        return $list;
    }

    /**
     * Return sale participation flag
     *
     * @param \XLite\Model\Product $product Product model
     *
     * @return boolean
     */
    protected function participateSaleAdmin(\XLite\Model\Product $product)
    {
        return $product->getParticipateSale()
            && $product->getDisplayPrice() < $product->getDisplayPriceBeforeSale();
    }
}
