<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\FormField\Input\Text;

/**
 * Canadian Postal Code
 *
 */
class CanadianPostalCode extends \XLite\View\FormField\Input\Text
{
    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        $rules[] = 'custom[canadianPostalCode]';

        return $rules;
    }
}

