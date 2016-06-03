<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Service name for address fields items list
 */
class AddressFieldsServiceName extends \XLite\View\FormField\Input\Text
{
    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        $rules[] = 'custom[onlySmallLetterNumberUnder]';

        return $rules;
    }
}
