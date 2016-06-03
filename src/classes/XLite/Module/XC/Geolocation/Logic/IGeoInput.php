<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Logic;

/**
 * Geo input interface
 */
interface IGeoInput
{
    /**
     * Returns scalar representation of internal geo data.
     *
     * @return mixed
     */
    public function getData();

    /**
     * Returns hash of geo data, is used as key in cache.
     *
     * @return string
     */
    public function getHash();
}