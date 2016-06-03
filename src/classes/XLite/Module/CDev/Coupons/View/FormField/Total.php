<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\FormField;

/**
 * Total
 */
class Total extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/Coupons/total.js';

        return $list;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '../modules/CDev/Coupons/total.twig';
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();
        $rules[] = 'funcCall[validateCouponTotal]';

        return $rules;
    }
}
