<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * TCP/IP port 
 */
class TCPIPPort extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 5;
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        $rules[] = 'min[0]';
        $rules[] = 'max[65535]';

        return $rules;
    }

}

