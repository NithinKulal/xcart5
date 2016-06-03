<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Logic;

/**
 * Geolocation endpoint
 */
class Geolocation extends \XLite\Base\Singleton
{
    /**
     * @var string Geolocation cache cell
     */
    const GEOLOCATION_SESSION_CELL = 'geolocation_cell';

    /**
     * Returns scalar representation of internal geo data.
     *
     * @param IGeoInput $data     Input data (ip or coords, etc.)
     * @param boolean   $useCache Flag to use session cache, true by default
     * @param boolean   $fastMode Stop searching on preferred provider
     *
     * @return array
     */
    public function getLocation(IGeoInput $data, $useCache = true, $fastMode = true)
    {
        $location = $useCache && isset(\XLite\Core\Session::getInstance()->{static::GEOLOCATION_SESSION_CELL})
                         ? \XLite\Core\Session::getInstance()->{static::GEOLOCATION_SESSION_CELL}
                         : null;
        if ($data && empty($location)) {
            $suitable = array();
            $parts = explode('\\', get_class($data));
            $type = array_pop($parts);
            foreach ($this->getProviders() as $providerClass) {
                $provider = new $providerClass;
                if ($provider && in_array($type, $provider->acceptedInput())) {
                    (\XLite\Core\Config::getInstance() && $providerClass === \XLite\Core\Config::getInstance()->XC->Geolocation->default_provider)
                        ? array_unshift($suitable, $provider)
                        : $suitable[] = $provider;
                    if ($fastMode) {
                        break;
                    }
                }
            }

            if ($suitable) {
                foreach ($suitable as $provider) {
                    $location = $provider->getLocation($data);
                    if ($location) {
                        if ($useCache) {
                            \XLite\Core\Session::getInstance()->{static::GEOLOCATION_SESSION_CELL} = $location;
                        }
                        break;
                    }
                }

            }
        }

        return $location;
    }

    /**
     * Sets cached location.
     *
     * @param array $location Location to set
     */
    public function setCachedLocation(array $location)
    {
        \XLite\Core\Session::getInstance()->{static::GEOLOCATION_SESSION_CELL} = $location;
    }

    /**
     * Returns provider classes list
     *
     * @return mixed
     */
    public function getProviders()
    {
        return array(
            'XLite\Module\XC\Geolocation\Model\Geolocation\MaxMindGeoIP'
        );
    }
}
