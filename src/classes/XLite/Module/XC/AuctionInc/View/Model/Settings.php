<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\Model;

/**
 * Auction inc configuration form model
 */
class Settings extends \XLite\View\Model\AShippingSettings
{
    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        switch ($option->getName()) {
            case 'entryPointSeparator':
            case 'entryPointDHL':
            case 'DHLAccessKey':
            case 'entryPointFEDEX':
            case 'entryPointUPS':
            case 'entryPointUSPS':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'accountId' => array(''),
                    ),
                );
                break;

            case 'fallbackRateValue':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'fallbackRate' => array('I', 'O'),
                    ),
                );
                break;

            case 'package':
            case 'insurable':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'calculationMethod' => array('C', 'CI'),
                    ),
                );
                break;

            case 'fixedFeeMode':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'calculationMethod' => array('F'),
                    ),
                );
                break;

            case 'fixedFeeCode':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'calculationMethod' => array('F'),
                        'fixedFeeMode'      => array('C'),
                    ),
                );
                break;

            case 'fixedFee1':
            case 'fixedFee2':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'calculationMethod' => array('F'),
                        'fixedFeeMode'      => array('F'),
                    ),
                );
                break;
        }

        return $cell;
    }
}
