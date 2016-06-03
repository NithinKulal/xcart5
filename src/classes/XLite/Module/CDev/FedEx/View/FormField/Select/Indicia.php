<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\View\FormField\Select;

/**
 * Indicia selector for settings page
 */
class Indicia extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'MEDIA_MAIL'                        => 'MEDIA_MAIL',
            'PARCEL_SELECT'                     => 'PARCEL_SELECT (1 LB through 70 LBS)',
            'PRESORTED_BOUND_PRINTED_MATTER'    => 'PRESORTED_BOUND_PRINTED_MATTER',
            'PRESORTED_STANDARD'                => 'PRESORTED_STANDARD (less than 1 LB)',
            'PARCEL_RETURN'                     => 'PARCEL_RETURN',
        );
    }
}