<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Controller\Customer;

/**
 * Abstract customer 
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Get fingerprint difference
     *
     * @params array $old Old fingerprint
     * @params array $new New fingerprint
     *
     * @return array
     */
    protected function getCartFingerprintDifference(array $old, array $new)
    {
        $diff = parent::getCartFingerprintDifference($old, $new);

        $cellKeys = array(
            'capostOfficeId',
            'capostShippingZipCode',
        );

        foreach ($cellKeys as $name) {

            if ($old[$name] != $new[$name]) {

                $diff[$name] = $new[$name];
            }
        }

        return $diff;
    }
}
