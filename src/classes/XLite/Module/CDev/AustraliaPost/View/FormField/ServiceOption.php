<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AustraliaPost\View\FormField;

/**
 * Service option selector for settings page
 */
class ServiceOption extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();
        $serviceOptions
            = \XLite\Module\CDev\AustraliaPost\Model\Shipping\Processor\AustraliaPost::getAuspostServiceOptions();

        foreach ($serviceOptions as $code => $name) {
            $list[$code] = $name;
        }

        return $list;
    }
}
