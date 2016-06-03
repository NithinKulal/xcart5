<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Customer;

/**
 * ThemeTweaker controller
 */
class ThemeTweaker extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'get_tree';

        return $list;
    }

    protected function doActionGetTree()
    {
        $treeKey = \XLite\Core\Request::getInstance()->treeKey;
        $session = \XLite\Core\Session::getInstance();

        if ($treeKey && $session->{$treeKey}) {

            header('Content-type: application/json');

            echo $session->{$treeKey};
            unset($session->{$treeKey});
            exit;
        }
    }
}
