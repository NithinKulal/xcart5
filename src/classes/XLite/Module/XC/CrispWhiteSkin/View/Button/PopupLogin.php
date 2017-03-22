<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Button;

/**
 * Login form in popup
 */
class PopupLogin extends \XLite\View\Button\PopupLogin implements \XLite\Base\IDecorator
{
    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target' => 'login',
            'widget' => '\XLite\View\Authorization',
            'fromURL' => \XLite::getController()->getURL()
        );
    }
}
