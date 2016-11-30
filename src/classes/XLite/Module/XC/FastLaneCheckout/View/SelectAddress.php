<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Pick address from address book
 */
abstract class SelectAddress extends \XLite\View\SelectAddress implements \XLite\Base\IDecorator
{
    /**
     * Returns true if add new address button should be visible in address book
     * 
     * @return boolean
     */
    protected function isAddAddressButtonVisible()
    {
        return FastLaneCheckout\Main::isFastlaneEnabled();
    }

    /**
     * Returns address book type
     * 
     * @return string
     */
    protected function getAddressType()
    {
        return \XLite\Core\Request::getInstance()->atype;
    }
}
