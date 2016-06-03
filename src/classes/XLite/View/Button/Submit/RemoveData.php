<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Submit;

/**
 * Submit button
 */
class RemoveData extends \XLite\View\Button\Submit\ConfirmWithPassword
{
    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $list = parent::prepareURLParams();
        $list['widget'] = 'XLite\View\Confirm\RemoveData';

        return $list;
    }
}
