<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View;

/**
 * Logo
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Return theme common files
     *
     * @param boolean $adminZone Admin zone flag OPTIONAL
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);
        $list[static::RESOURCE_CSS][] = 'modules/CDev/SimpleCMS/page/style.css';

        return $list;
    }

    /**
     * Return favicon resource path
     *
     * @return string
     */
    protected function getFavicon()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->favicon);

        return $url ?: parent::getFavicon();
    }

    /**
     * Flag if the favicon is displayed in the customer area
     *
     * If the custom favicon is defined then the favicon will be displayed
     *
     * @return boolean
     */
    protected function displayFavicon()
    {
        return parent::displayFavicon() || (bool)\XLite\Core\Config::getInstance()->CDev->SimpleCMS->favicon;
    }
}
