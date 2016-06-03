<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Model\Geolocation;

use XLite\Module\XC\Geolocation\Logic;

/**
 * Abstract geolocation provider
 */
abstract class AProvider
{
    /**
     * Returns geolocation address in XCart format
     *
     * @param Logic\IGeoInput $data
     *
     * @return array
     */
    public function getLocation(Logic\IGeoInput $data)
    {
        $raw = $this->getRawLocation($data);

        return $raw ? $this->transformData($raw) : null;
    }

    /**
     * Returns human readable provider name, classname by default.
     *
     * @return string
     */
    public function getProviderName()
    {
        $parts = explode('\\', get_called_class());

        return array_pop($parts);
    }

    /**
     * Returns geolocation data in raw format (defined by provider)
     *
     * @param Logic\IGeoInput $data
     *
     * @return mixed
     */
    abstract public function getRawLocation(Logic\IGeoInput $data);

    /**
     * Returns list of accepted geo input types.
     *
     * @return array
     */
    abstract public function acceptedInput();

    /**
     * Transforms raw geolocation data to XCart format (an array of address fields)
     *
     * @param mixed $data
     *
     * @return array
     */
    abstract protected function transformData($input);
}