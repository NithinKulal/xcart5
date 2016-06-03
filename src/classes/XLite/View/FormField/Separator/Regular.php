<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Separator;


/**
 * \XLite\View\FormField\Separator\Regular
 */
class Regular extends \XLite\View\FormField\Separator\ASeparator
{
    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'regular.twig';
    }
}
