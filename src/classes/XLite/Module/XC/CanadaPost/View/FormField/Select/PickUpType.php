<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\FormField\Select;

/**
 * Parcel puck up type selector
 */
class PickUpType extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            \XLite\Module\XC\CanadaPost\Core\API::PICKUP_TYPE_AUTO   => static::t('shipments are picked up by Canada Post'),
            \XLite\Module\XC\CanadaPost\Core\API::PICKUP_TYPE_MANUAL => static::t('deposit your items at a Post Office'),
        );
    }
}

