<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\CDev\MarketPrice\View\Product;

/**
 * Product list item widget
 */
class ListItem extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $product = $this->getProduct();

        $label = \XLite\Module\CDev\MarketPrice\Core\Labels::getLabel($product);

        if ($product->getMarketPrice() && !$label) {
            $widget = $this->getWidget(
                array(
                    'product'   => $product,
                ),
                'XLite\View\Price'
            );
            $widget->getMarketPriceLabel();
        }

        return parent::getLabels() + \XLite\Module\CDev\MarketPrice\Core\Labels::getLabel($product);
    }
}