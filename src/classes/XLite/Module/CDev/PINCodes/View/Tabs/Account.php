<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\Tabs;

/**
 * Tabs related to user profile section
 */
abstract class Account extends \XLite\View\Tabs\Account implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'pin_codes';
        
        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        if ($this->isLogged()) {
            $cnd = new \XLite\Core\CommonCell;
            $cnd->user = \XLite\Core\Auth::getInstance()->getProfile();

            $count = \XLite\Core\Database::getRepo('XLite\Model\Order')->searchWithPinCodes($cnd, true);

            if ($count > 0) {
                $list['pin_codes'] = [
                    'weight'   => 400,
                    'title'    => static::t('PIN codes'),
                    'template' => 'modules/CDev/PINCodes/account_pin_codes.twig',
                ];
            }
        }

        return $list;
    }
}
