<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Model;

use \XLite\Module\XC\Geolocation\Logic;

/**
 * Address model
 *
 * @HasLifecycleCallbacks
 */
abstract class Address extends \XLite\Model\Address implements \XLite\Base\IDecorator
{
    /**
     * Get default value for the field
     *
     * @param string $fieldName Field service name
     *
     * @return mixed
     */
    public static function getDefaultFieldValue($fieldName)
    {
        $result = null;
        $location = static::shouldAccessLocation()
            ? Logic\Geolocation::getInstance()->getLocation(new Logic\GeoInput\IpAddress)
            : null;

        if ($location) {
            $fieldValue = isset($location[$fieldName]) ? $location[$fieldName] : null;

            switch ($fieldName) {
                case 'country':
                    if ($fieldValue) {
                        $result = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneByCode($fieldValue);
                        $result = $result ?: null;
                    }
                    break;

                case 'state':
                    if ($fieldValue) {
                        $result = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneBy(array('code' => $fieldValue));
                        $result = $result ?: null;
                    }
                    if (!$result || (!$fieldValue && isset($location['country']))) {
                        $result = \XLite\Core\Database::getRepo('XLite\Model\State')->findByCountryCode($location['country']);
                        $result = $result ? $result[0] : null;
                    }
                    break;

                case 'custom_state':
                case 'zipcode':
                case 'city':
                    $result = $fieldValue ?: '';
                    break;

                default:
            }
        }
        $result = (null !== $result) ? $result : parent::getDefaultFieldValue($fieldName);

        return $result;
    }

    /**
     * Returns true if geolocation should be accessed
     */
    public static function shouldAccessLocation()
    {
        return !(\XLite::getController() instanceof \XLite\Controller\Customer\ACheckoutReturn);
    }
}
