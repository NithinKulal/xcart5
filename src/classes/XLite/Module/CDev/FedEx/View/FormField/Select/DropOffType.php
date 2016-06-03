<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\View\FormField\Select;

/**
 * DropOff type selector for settings page
 */
class DropOffType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'REGULAR_PICKUP'          => 'Regular pickup',
            'REQUEST_COURIER'         => 'Request courier',
            'DROP_BOX'                => 'Drop box',
            'BUSINESS_SERVICE_CENTER' => 'Business Service Center',
            'STATION'                 => 'Station',
        );
    }
}
