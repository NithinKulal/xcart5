<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Logic;

/**
 * Price before sale
 */
class PriceBeforeSale extends \XLite\Logic\Price
{
    /**
     * Define modifiers
     *
     * @return array
     */
    protected function defineModifiers()
    {
        $modifiers = parent::defineModifiers();
        foreach ($modifiers as $i => $modifier) {
            if (0 === strpos($modifier->getClass(), 'XLite\\Module\\CDev\Sale\\')) {
                unset($modifiers[$i]);
            }
        }

        return $modifiers;
    }
}
