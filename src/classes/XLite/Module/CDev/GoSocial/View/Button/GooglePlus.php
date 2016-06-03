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
class GooglePlus extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoSocial/button/google_plus.twig';
    }

    /**
     * Get button attributes
     *
     * @return array
     */
    protected function getButtonAttributes()
    {
        return array(
            'href' => \XLite::getInstance()->getShopURL($this->getURL()),
            'size' => 'medium',
        );
    }

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

}
