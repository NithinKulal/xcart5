<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Cache;

/**
 * CacheKeyPartsGenerator contains the common logic that is used by different widgets to obtain cache key parts such as membership and shipping zones.
 */
class CacheKeyPartsGenerator
{
    /**
     * Get logged in customer's membership that can be used as a cache key part
     *
     * @return string|null
     */
    public function getMembershipPart()
    {
        $auth = \XLite\Core\Auth::getInstance();

        if (!$auth->isLogged()) {
            return null;
        }

        $profile = $auth->getProfile();

        return $profile->getMembership() ? $profile->getMembership()->getMembershipId() : null;
    }

    /**
     * Get logged in customer's shipping zones string that can be used as a cache key part
     *
     * @return string|null
     */
    public function getShippingZonesPart()
    {
        $auth = \XLite\Core\Auth::getInstance();

        if (!$auth->isLogged()) {
            return null;
        }

        $zones = [];

        $profile = $auth->getProfile();

        $repo = \XLite\Core\Database::getRepo('XLite\Model\Zone');
        $address = \XLite\Model\Shipping::prepareAddressData($profile->getShippingAddress());

        foreach ($repo->findApplicableZones($address) as $zone) {
            $zones[] = $zone->getZoneId();
        }

        return implode(',', $zones);
    }
}
