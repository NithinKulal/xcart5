<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\AccessLevel
 */
class AccessLevel extends \XLite\View\FormField\Select\Regular
{
    /**
     * Determines if this field is visible for customers or not
     *
     * @var boolean
     */
    protected $isAllowedForCustomer = false;


    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = \XLite\Core\Auth::getInstance()->getUserTypesRaw();

        foreach ($list as $k => $v) {
            if (
                !\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage admins') 
                && $k == \XLite\Core\Auth::getInstance()->getAdminAccessLevel()
            ) {
                unset($list[$k]);
            }else{
                $list[$k] = static::t($v);
            }
        }

        return $list;
    }

    /**
     * Check field value validity
     *
     * @return boolean
     */
    protected function checkFieldValue()
    {
        $isAllowedForCurrentUser = TRUE;
        if (!\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage admins')
            && $this->getValue() == \XLite\Core\Auth::getInstance()->getAdminAccessLevel()) {
            $isAllowedForCurrentUser = FALSE;
        }
        return $isAllowedForCurrentUser && in_array($this->getValue(), \XLite\Core\Auth::getInstance()->getAccessLevelsList());
    }
}
