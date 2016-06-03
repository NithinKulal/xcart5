<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AustraliaPost\View\FormField;

/**
 * Package box type selector for settings page
 */
class PackageBoxType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        /** @var \XLite\Model\Shipping\Method $method */
        $method = \XLite::getController()->getMethod();
        $processor = $method->getProcessorObject();
        $packageBoxTypeOptions = $processor->getPackageBoxTypeOptions();

        foreach ($packageBoxTypeOptions as $option) {
            $list[$option['code']] = $option['name'];
        }

        return $list;
    }
}
