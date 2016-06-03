<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AustraliaPost\View\Form;

/**
 * Test shipping rates widget
 */
class TestRates extends \XLite\View\Form\ATestRates
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'aupost';
    }
}
