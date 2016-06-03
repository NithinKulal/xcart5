<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

/**
 * Add2CartPopup products list class extension
 *
 * @Decorator\Depend("XC\Add2CartPopup")
 */
class Add2CartProducts extends \XLite\Module\XC\Add2CartPopup\View\Products implements \XLite\Base\IDecorator
{
    /**
     * Return products list: temporary disable coming-soon products to exclude them from result
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return mixed
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $oldCsEnabled = \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled;

        \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled = false;

        $result = parent::getData($cnd, $countOnly);

        \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled = $oldCsEnabled;

        return $result;
    }
}
