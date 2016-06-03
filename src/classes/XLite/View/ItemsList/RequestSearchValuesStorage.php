<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

/**
 * RequestSearchValuesStorage
 */
class RequestSearchValuesStorage extends \XLite\View\ItemsList\ASearchValuesStorage implements \XLite\View\ItemsList\ISearchValuesStorage
{
    /**
     * Request data
     */
    protected $requestData;

    /**
     * @param array[mixed]    $requestData Request params data array
     */
    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * Get param value
     *
     * @param string    $serviceName   Search condition service name
     * @param mixe      $value
     */
    public function setValue($serviceName, $value)
    {
        if ($value === null) {
            unset($this->requestData[$serviceName]);
        } else {
            $this->requestData[$serviceName] = $value;
        }

        if ($this->fallbackStorage) {
            $this->fallbackStorage->setValue($serviceName, $value);
        }
    }

    /**
     * Get param value
     *
     * @param string    $serviceName   Search condition service name
     *
     * @return mixed
     */
    protected function getInnerValue($serviceName)
    {
        return isset($this->requestData[$serviceName])
            ? $this->requestData[$serviceName]
            : null;
    }

    /**
     * Update storage
     */
    protected function updateInner()
    {
        \XLite\Core\Request::getInstance()->mapRequest($this->requestData);
    }
}
