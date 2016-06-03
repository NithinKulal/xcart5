<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Password
 */
class Password extends \XLite\View\FormField\Input\Secure
{
    /**
     * setCommonAttributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $result = parent::setCommonAttributes($attrs);
        $result['value'] = '';

        return $result;
    }

    /**
     * Return true if value is trusted (purification must be ignored)
     *
     * @return boolean
     */
    public function isTrusted()
    {
        return true;
    }
}
