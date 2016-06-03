<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Event task
 */
class EventTask extends \XLite\Base\Singleton
{
    const STATE_STANDBY     = 1;
    const STATE_IN_PROGRESS = 2;
    const STATE_FINISHED    = 3;
    const STATE_ABORTED     = 4;

    /**
     * Driver
     *
     * @var \XLite\Core\EventDriver\AEventDriver
     */
    protected $driver;

    /**
     * Call events
     *
     * @param string $name Event name
     * @param array  $args Event arguments OPTIONAL
     *
     * @return boolean
     */
    public static function __callStatic($name, array $args = array())
    {
        $result = false;

        if (in_array($name, \XLite\Core\EventListener::getInstance()->getEvents())) {
            $args = isset($args[0]) && is_array($args[0]) ? $args[0] : array();
            $driver = static::getInstance()->getDriver();
            $result = $driver ? $driver->fire($name, $args) : false;
        }

        return $result;
    }

    /**
     * Get driver
     *
     * @return \XLite\Core\EventDriver\AEventDriver
     */
    public function getDriver()
    {
        if (!isset($this->driver)) {
            $driver = \XLite::GetInstance()->getOptions(array('other', 'event_driver')) ?: 'auto';
            $driver = strtolower($driver);
            $list = $this->getDrivers();

            if ('auto' != $driver) {
                foreach ($list as $class) {
                    if (strtolower($class::getCode()) == $driver) {
                        $this->driver = new $class;
                        break;
                    }
                }
            }

            if (!$this->driver) {
                $this->driver = $list ? new $list[0] : false;
            }
        }

        return $this->driver;
    }

    /**
     * Get valid drivers
     *
     * @return array
     */
    protected function getDrivers()
    {
        $list = array();

        foreach ($this->getDriversClasses() as $class) {
            if ($class::isValid()) {
                $list[] = $class;
            }
        }

        return $list;
    }

    /**
     * Get drivers classes
     *
     * @return array
     */
    protected function getDriversClasses()
    {
        return array(
            '\XLite\Core\EventDriver\Db',
        );
    }
}
