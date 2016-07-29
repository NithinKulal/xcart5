<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\FormField\Input\Text;

/**
 * URL (with IPv6 host) 
 *
 */
class URLipv6 extends \XLite\View\FormField\Input\Text\URL
{
    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        $key = array_search('custom[url]', $rules);

        if (false !== $key) {
            unset($rules[$key]);
        }

        $rules[] = 'funcCall[checkURLipv6]';

        return $rules;
    }
}
