<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View;

/**
 * Product page widgets collection
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class VariantsProductPageCollection extends \XLite\View\ProductPageCollection implements \XLite\Base\IDecorator
{
    /**
     * Register the view classes collection
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        return array_merge(
            parent::defineWidgetsCollection(),
            array(
                '\XLite\View\Product\Details\Customer\Quantity'
            )
        );
    }

    /**
     * Check - allowed display subwidget or not
     *
     * @param string $name Widget class name
     *
     * @return boolean
     */
    protected function isAllowedWidget($name)
    {
        $result = parent::isAllowedWidget($name);

        if (!$result && $name === '\XLite\View\Product\Details\Customer\Quantity') {
            $result = true;
        }

        return $result;
    }
}
