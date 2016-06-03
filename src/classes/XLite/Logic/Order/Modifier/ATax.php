<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Order\Modifier;

/**
 * Abstract tax modifier
 */
abstract class ATax extends \XLite\Logic\Order\Modifier\AModifier
{
    /**
     * Modifier type (see \XLite\Model\Base\Surcharge)
     *
     * @var string
     */
    protected $type = \XLite\Model\Base\Surcharge::TYPE_TAX;

    /**
     * Sorting weight
     *
     * @var integer
     */
    protected $sortingWeight = 100;

    /**
     * Get default customer address for taxes calculation
     *
     * @return array
     */
    protected function getDefaultAddress()
    {
        return \XLite\Model\Shipping::getDefaultAddress();
    }
}
