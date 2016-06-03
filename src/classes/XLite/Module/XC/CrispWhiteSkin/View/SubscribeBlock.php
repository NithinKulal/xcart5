<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * SubscribeBlock decorator
 *
 * @Decorator\Depend("XC\NewsletterSubscriptions")
 */
abstract class SubscribeBlock extends \XLite\Module\XC\NewsletterSubscriptions\View\SubscribeBlock implements \XLite\Base\IDecorator
{
    /**
     * Check if form input is field only
     *
     * @return boolean
     */
    public function isFieldOnly()
    {
        return false;
    }
}
