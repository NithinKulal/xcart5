<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\ItemsList\Model;

/**
 * Category view model
 *
 */
class Attribute extends \XLite\View\ItemsList\Model\Attribute implements \XLite\Base\IDecorator
{
    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Get switcher field
     *
     * @return array
     */
    protected function getSwitcherField()
    {
        return array(
            'class'  => 'XLite\Module\XC\ProductFilter\View\FormField\Inline\Input\Checkbox\Switcher\Filter',
            'name'   => 'visible',
            'params' => array(
                'switcherIcon' => 'fa-filter',
                'offLabel' => 'Hidden from products filter',
                'onLabel' => 'Visible in products filter',
            ),
        );
    }
}