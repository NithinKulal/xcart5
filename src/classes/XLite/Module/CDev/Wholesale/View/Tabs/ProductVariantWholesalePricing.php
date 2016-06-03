<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\Tabs;

/**
 * Tabs related to Wholesale pricing pages
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class ProductVariantWholesalePricing extends \XLite\Module\CDev\Wholesale\View\Tabs\WholesalePricing implements \XLite\Base\IDecorator
{
    /**
     * Returns tab URL
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return 'product_variant' == $this->getTarget()
            ? $this->buildURL(
                'product_variant',
                '',
                array(
                    'page'  => 'wholesale_pricing',
                    'id'    => $this->getProductVariant()->getId(),
                    'spage' => $target
                )
            ) : parent::buildTabURL($target);
    }
}
