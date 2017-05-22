<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Form\ItemsList\Messages\Customer;

/**
 * Customer order messages
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XLite\Module\XC\VendorMessages\View\Form\ItemsList\Messages\Customer\Order implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return parent::getDefaultClassName()
            . (\XLite\Module\XC\VendorMessages\Main::isVendorAllowed() ? ' multivendor-enabled' : '');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['recipient_id'] = \XLite\Core\Request::getInstance()->recipient_id;

        return $list;
    }

}
