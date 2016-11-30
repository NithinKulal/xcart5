<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Recently viewed products widget
 *
 * @Decorator\Depend ("CDev\ProductAdvisor")
 */
class RecentlyViewed extends \XLite\Module\CDev\ProductAdvisor\View\RecentlyViewed implements \XLite\Base\IDecorator
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        if ($key = array_search('main', $result)) {
            unset($result[$key]);
        }

        return $result;
    }
}