<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Pager\Customer\Order;

abstract class AOrder extends \XLite\View\Pager\Customer\Order\AOrder implements \XLite\Base\IDecorator
{
    protected function getPerPageCounts()
    {
        return [1, 3, 6, 12, 24, 36];
    }
}
