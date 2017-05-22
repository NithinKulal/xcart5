<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Menu\Admin\LeftMenu\Info;

/**
 * Messages count
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class MessagesMultivendor extends \XLite\Module\XC\VendorMessages\View\Menu\Admin\LeftMenu\Info\Messages implements \XLite\Base\IDecorator
{

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return (!\XLite\Core\Auth::getInstance()->isVendor() || \XLite\Module\XC\VendorMessages\Main::isVendorAllowed())
            && parent::isVisible();
    }

    /**
     * @inheritdoc
     */
    protected function getCounter()
    {
        return \XLite\Core\Auth::getInstance()->isVendor()
            ? \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->countUnreadForVendor()
            : \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->countUnreadForAdmin();
    }

    /**
     * @inheritdoc
     */
    protected function getTargetProfileId()
    {
        return (\XLite\Core\Auth::getInstance()->isVendor() && \XLite\Module\XC\VendorMessages\Main::isVendorAllowed())
            ? \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()
            : parent::getTargetProfileId();
    }


}
