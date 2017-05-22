<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Customer;

/**
 * All customer messages
 */
class All extends \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base\All
{

    /**
     * @inheritdoc
     */
    protected function getPagerClass()
    {
        return '\XLite\Module\XC\VendorMessages\View\Pager\Message\Customer\All';
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $condition = parent::getSearchCondition();

        $condition->{\XLite\Model\Repo\Order::P_PROFILE} = \XLite\Core\Auth::getInstance()->getProfile();

        return $condition;
    }


}
