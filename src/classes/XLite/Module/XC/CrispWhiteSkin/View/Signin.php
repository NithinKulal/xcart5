<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Signin
 */
abstract class Signin extends \XLite\View\Signin implements \XLite\Base\IDecorator
{
    protected function getWrapperStyleClass()
    {
        return 'signin-wrapper';
    }
}
