<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\FormField\Input;

/**
 * Logo
 */
class AppleIcon extends \XLite\Module\CDev\SimpleCMS\View\FormField\Input\AImage
{
    /**
     * Return the image URL value
     *
     * @return string
     */
    protected function getImage()
    {
        return $this->getAppleIcon();
    }

    /**
     * Return the default label
     *
     * @return string
     */
    protected function getReturnToDefaultLabel()
    {
        return 'Return to default AppleIcon';
    }

    /**
     * Return the inner name for widget.
     * It is used in model widget identification of the "useDefaultImage" value
     *
     * @return string
     */
    protected function getImageName()
    {
        return 'appleIcon';
    }

}
