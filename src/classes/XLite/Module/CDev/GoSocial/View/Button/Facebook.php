<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Button;

/**
 * Facebook button
 *
 * @ListChild (list="buttons.share", weight="100")
 */
class Facebook extends \XLite\Module\CDev\GoSocial\View\Button\ASocialButton
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
        && \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_share_use;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonType()
    {
        return self::BUTTON_CLASS_FACEBOOK;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonLabel()
    {
        return static::t('Share');
    }
}