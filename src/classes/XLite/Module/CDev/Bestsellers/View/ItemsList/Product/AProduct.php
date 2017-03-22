<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Bestsellers\View\ItemsList\Product;

/**
 * Product list
 */
abstract class AProduct extends \XLite\View\ItemsList\Product\AProduct implements \XLite\Base\IDecorator
{
    /**
     * Allowed sort criteria
     */
    const SORT_BY_MODE_BOUGHT = 'p.sales';
}
