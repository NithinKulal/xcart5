<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\View\FormField\Select;

/**
 * Packaging type selector for settings page
 */
class PackagingType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Packages parameters: weight (lbs), dimensions (inches)
     *
     * @var array
     */
    protected static $upsPackages = array(
        '00' => array(
            'name' => 'Unknown',
            'limits' => array(
                'weight' => 150,
                'length' => 108,
                'width' => 108,
                'height' => 108
            )
        ),
        '01' => array(
            'name' => 'UPS Letter / UPS Express Envelope',
            'limits' => array(
                'weight' => 1,
                'length' => 9.5,
                'width' => 12.5,
                'height' => 0.25
            )
        ),
        '02' => array(
            'name' => 'Package'
        ),
        '03' => array(
            'name' => 'UPS Tube',
            'limits' => array(
                'length' => 6,
                'width' => 38,
                'height' => 6
            )
        ),
        '04' => array(
            'name' => 'UPS Pak',
            'limits' => array(
                'length' => 12.75,
                'width' => 16,
                'height' => 2
            )
        ),
        '21' => array(
            'name' => 'UPS Express Box',
            'limits' => array(
                'length' => 13,
                'width' => 18,
                'height' => 3,
                'weight' => 30
            )
        ),
        '24' => array(
            'name' => 'UPS 25 Kg Box&#174;',
            'limits' => array(
                'length' => 17.375,
                'width' => 19.375,
                'height' => 14,
                'weight' => 55.1
            )
        ),
        '25' => array(
            'name' => 'UPS 10 Kg Box&#174;',
            'limits' => array(
                'length' => 13.25,
                'width' => 16.5,
                'height' => 10.75,
                'weight' => 22
            )
        ),
        '30' => array(
            'name' => 'Pallet (for GB or PL domestic shipments only)'
        ),
        '2a' => array(
            'name' => 'Small Express Box'
        ),
        '2b' => array(
            'name' => 'Medium Express Box'
        ),
        '2c' => array(
            'name' => 'Large Express Box'
        )
    );

    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();
        foreach (static::$upsPackages as $key => $option) {
            $list[$key] = static::t($option['name']);
        }

        return $list;
    }
}
