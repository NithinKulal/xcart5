<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Controller\Customer;

/**
 * Add2CartPopup settings page controller
 *
 * @Decorator\Depend("XC\Add2CartPopup")
 */
class Add2CartPopup extends \XLite\Module\XC\Add2CartPopup\Controller\Customer\Add2CartPopup implements \XLite\Base\IDecorator
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('You just added');
    }
}
