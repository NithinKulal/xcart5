<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer\Page;

/**
 * QuickLook
 *
 * @ListChild (list="center")
 */
class QuickLook extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'quick_look';

        return $list;
    }


    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/quick_look';
    }
}
