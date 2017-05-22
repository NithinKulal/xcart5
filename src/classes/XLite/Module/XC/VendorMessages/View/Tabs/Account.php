<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Tabs;

/**
 * Tabs related to user profile section
 */
class Account extends \XLite\View\Tabs\Account implements \XLite\Base\IDecorator
{

    /**
     * @inheritdoc
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'messages';

        return $list;
    }

    /**
     * @inheritdoc     *
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/VendorMessages/order/list/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineTabs()
    {
        return parent::defineTabs()
            + array(
                'messages' => array(
                    'title'    => 'Messages',
                    'template' => 'modules/XC/VendorMessages/page/messages.twig',
                    'weight'   => 400,
                )
            );
    }

    /**
     * @inheritdoc
     */
    protected function getTabLinkTemplate(array $tab)
    {
        return $tab['template'] == 'modules/XC/VendorMessages/page/messages.twig'
            ? 'modules/XC/VendorMessages/tabs/account/messages.twig'
            : parent::getTabLinkTemplate($tab);
    }

    /**
     * Get unread messages count
     *
     * @return integer|boolean
     */
    protected function countMessages()
    {
        return \XLite\Core\Auth::getInstance()->isLogged()
            ? \XLite\Core\Auth::getInstance()->getProfile()->countOwnUnreadMessages()
            : false;
    }

}
