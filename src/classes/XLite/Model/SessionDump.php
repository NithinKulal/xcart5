<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Session (dump)
 */
class SessionDump extends \XLite\Model\Session
{
   /**
     * Temporary data 
     * 
     * @var array
     */
    protected $temporaryData = array();

    /**
     * Session cell getter
     *
     * @param string $name Cell name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->temporaryData[$name]) ? $this->temporaryData[$name] : null;
    }

    /**
     * Session cell setter
     *
     * @param string $name  Cell name
     * @param mixed  $value Value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->temporaryData[$name] = $value;
    }

    /**
     * Check - set session cell with specified name or not
     *
     * @param string $name Cell name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->temporaryData[$name]);
    }

    /**
     * Remove session cell
     *
     * @param string $name Cell name
     *
     * @return void
     */
    public function __unset($name)
    {
        if (isset($this->temporaryData[$name])) {
            unset($this->temporaryData[$name]);
        }
    }

    /**
     * Unset in batch mode
     *
     * @param string $name Cell name
     *
     * @return void
     */
    public function unsetBatch($name)
    {
        foreach (func_get_args() as $name) {
            if (isset($this->temporaryData[$name])) {
                unset($this->temporaryData[$name]);
            }
        }
    }

    /**
     * Get session cell by name
     *
     * @param string  $name        Cell name
     * @param boolean $ignoreCache Flag: true - ignore cells cache OPTIONAL
     *
     * @return \XLite\Model\SessionCell|void
     */
    protected function getCellByName($name, $ignoreCache = false)
    {
        return null;
    }

    /**
     * Set session cell value
     *
     * @param string $name  Cell name
     * @param mixed  $value Value to set
     *
     * @return void
     */
    protected function setCellValue($name, $value)
    {
        $this->temporaryData[$name] = $value;
    }

}
