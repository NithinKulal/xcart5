<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * URL
 */
class URL extends \XLite\View\FormField\Input\Text
{
    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $result = parent::checkFieldValidity();

        if ($result && $this->getValue()) {
            $parts = @parse_url($this->getValue());
            if (!$parts || !isset($parts['scheme']) || !isset($parts['host'])) {
                $result = false;
                $this->errorMessage = \XLite\Core\Translation::lbl(
                    'The value of the X field has an incorrect format',
                    array(
                        'name' => $this->getLabel(),
                    )
                );
            }
        }

        return $result;
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        $rules[] = 'custom[url]';

        return $rules;
    }
}
