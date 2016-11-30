<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Core;

/**
 * Layout manager
 */
class Layout extends \XLite\Core\Layout implements \XLite\Base\IDecorator
{
    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo);

        return $url ?: parent::getLogo();
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getAppleIcon()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->appleIcon);

        return $url ?: parent::getAppleIcon();
    }

}
