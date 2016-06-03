<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Order\Modifier;

/**
 * Abstract shipping modifier
 */
abstract class AShipping extends \XLite\Logic\Order\Modifier\AModifier
{
    /**
     * Modifier type (see \XLite\Model\Base\Surcharge)
     *
     * @var string
     */
    protected $type = \XLite\Model\Base\Surcharge::TYPE_SHIPPING;

    /**
     * Sorting weight
     *
     * @var integer
     */
    protected $sortingWeight = 200;

    // {{{ Widget

    /**
     * Get widget class
     *
     * @return string
     */
    public static function getWidgetClass()
    {
        return '\XLite\View\Order\Details\Admin\Modifier\Shipping';
    }

    // }}}
}
