<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Form;

/**
 * Images settings form
 */
class ImagesSettings extends \XLite\View\Form\ImagesSettings implements \XLite\Base\IDecorator
{
    /**
     * Add the 'enctype="multipart/form-data"' form attribute
     *
     * @return boolean
     */
    protected function isMultipart()
    {
        return true;
    }
}
