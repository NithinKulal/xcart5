<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\FormField\Select;

/**
 * Container selector for settings page
 */
class Container extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'VARIABLE'                     => 'Variable',
            'FLAT RATE ENVELOPE'           => 'Flat rate envelope',
            'PADDED FLAT RATE ENVELOPE'    => 'Padded flat rate envelope',
            'LEGAL FLAT RATE ENVELOPE'     => 'Legal flat rate envelope',
            'SM FLAT RATE ENVELOPE'        => 'SM flat rate envelope',
            'WINDOW FLAT RATE ENVELOPE'    => 'Window flat rate envelope',
            'GIFT CARD FLAT RATE ENVELOPE' => 'Gift card flat rate envelope',
            'FLAT RATE BOX'                => 'Flat rate box',
            'SM FLAT RATE BOX'             => 'SM flat rate box',
            'MD FLAT RATE BOX'             => 'MD flat rate box',
            'LG FLAT RATE BOX'             => 'LG flat rate box',
            'REGIONALRATEBOXA'             => 'Regional rate boxA',
            'REGIONALRATEBOXB'             => 'Regional rate boxB',
            'RECTANGULAR'                  => 'Rectangular',
            'NONRECTANGULAR'               => 'Non-rectangular',
        );
    }
}
