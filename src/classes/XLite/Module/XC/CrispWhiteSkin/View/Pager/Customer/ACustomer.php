<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Pager\Customer;

abstract class ACustomer extends \XLite\View\Pager\Customer\ACustomer implements \XLite\Base\IDecorator
{
    protected function getPerPageCounts()
    {
        return false;
    }
}
