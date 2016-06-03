<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View;

/**
 * Tabber
 */
class Tabber extends \XLite\View\Tabber implements \XLite\Base\IDecorator
{
    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return boolean
     */
    protected function isTabsNavigationVisible()
    {
        $result = parent::isTabsNavigationVisible();

        if ($result
            && 'product_variant' === $this->getTarget()
        ) {
            $result = 2 < count($this->getTabberPages());
        }

        return $result;
    }
}
