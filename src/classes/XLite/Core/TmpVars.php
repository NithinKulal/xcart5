<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * DB-based temporary variables
 */
class TmpVars extends \XLite\Base\Singleton
{
    /**
     * Getter
     *
     * @param string $name Name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return ($var = $this->getVar($name)) ? unserialize($var->getValue()) : null;
    }

    /**
     * Setter
     *
     * @param string $name  Name
     * @param mixed  $value Value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $var = $this->getVar($name);

        if (isset($value)) {
            $data = array('value' => serialize($value));
            if (isset($var)) {
                $this->getRepo()->update($var, $data, true);
            } else {
                $var = $this->getRepo()->insert($data + array('name' => $name), true);
            }
        } elseif ($var) {
            $this->getRepo()->delete($var, true);
        }
    }

    /**
     * Check if value is set
     *
     * @param string $name Variable name to check
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return !is_null($this->getVar($name));
    }

    /**
     * Search var in DB table
     *
     * @param string $name Var name
     *
     * @return \XLite\Model\TmpVar
     */
    protected function getVar($name)
    {
        return $this->getRepo()->findOneBy(array('name' => $name));
    }

    /**
     * Return the Doctrine repository
     *
     * @return \XLite\Model\Repo\TmpVar
     */
    protected function getRepo()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\TmpVar');
    }
}
