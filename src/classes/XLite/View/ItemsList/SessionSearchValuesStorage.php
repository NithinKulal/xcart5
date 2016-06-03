<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

/**
 * SessionSearchValuesStorage
 */
class SessionSearchValuesStorage extends \XLite\View\ItemsList\ASearchValuesStorage implements \XLite\View\ItemsList\ISearchValuesStorage
{
    /**
     * Session cell
     */
    protected $sessionCell;
    protected $sessionCellName;

    /**
     * @param string    $sessionCellName Session cell name
     */
    public function __construct($sessionCellName)
    {
        $this->sessionCellName = $sessionCellName;
        $this->sessionCell = \XLite\Core\Session::getInstance()->get($sessionCellName);
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
            unset($this->sessionCell[$serviceName]);
        } else {
            $this->sessionCell[$serviceName] = $value;
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
        return isset($this->sessionCell[$serviceName])
            ? $this->sessionCell[$serviceName]
            : null;
    }

    /**
     * Update storage
     */
    protected function updateInner()
    {
        \XLite\Core\Session::getInstance()->set($this->sessionCellName, $this->sessionCell);
    }
}
