<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Button;

/**
 * Google+ button
 *
 * @ListChild (list="buttons.share", weight="300")
 */
class GooglePlus extends \XLite\Module\CDev\GoSocial\View\Button\ASocialButton
{

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->CDev->GoSocial->gplus_use;
    }

    /**
     * The link caption that should be posted to the social networks. By default it’s the page’s title.
     *
     * @return string
     */
    protected function getDataTitle()
    {
        return $this->getTitle() ?: null;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonType()
    {
        return self::BUTTON_CLASS_GOOGLEPLUS;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonLabel()
    {
        return static::t('Plus');
    }
}
