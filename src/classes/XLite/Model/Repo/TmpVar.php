<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Temporary variables repository
 */
class TmpVar extends \XLite\Model\Repo\ARepo
{
    /**
     * Event task state prefix
     */
    const EVENT_TASK_STATE_PREFIX = 'eventTaskState.';

    /**
     * Set variable 
     * 
     * @param string $name  Variable name
     * @param mixed  $value Variable value
     * @param boolean  $flush Perform flush on return
     *  
     * @return void
     */
    public function setVar($name, $value, $flush = true)
    {
        $entity = $this->findOneBy(array('name' => $name));

        if (!$entity) {
            $entity = new \XLite\Model\TmpVar;
            $entity->setName($name);
            \XLite\Core\Database::getEM()->persist($entity);
        }

        if (!is_scalar($value)) {
            $value = serialize($value);
        }

        $entity->setValue($value);

        if ($flush) {
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Get variable 
     * 
     * @param string $name Variable name
     *  
     * @return mixed
     */
    public function getVar($name)
    {
        $entity = $this->findOneBy(array('name' => $name));

        $value = $entity ? $entity->getValue() : null;

        if (!empty($value)) {
            $tmp = @unserialize($value);
            if (false !== $tmp) {
                $value = $tmp;
            }
        }

        return $value;
    }

    // {{{ Event tasks-based temporary variable operations

    /**
     * Initialize event task state
     *
     * @param string $name    Event task name
     * @param array  $options Event options OPTIONAL
     *
     * @return array
     */
    public function initializeEventState($name, array $options = array())
    {
        $this->setEventState(
            $name,
            array('position' => 0, 'length' => 0, 'state' => \XLite\Core\EventTask::STATE_STANDBY) + $options
        );
    }

    /**
     * Get event task state 
     * 
     * @param string $name Event task name
     *  
     * @return array
     */
    public function getEventState($name)
    {
        return $this->getVar(static::EVENT_TASK_STATE_PREFIX . $name);
    }

    /**
     * Set event state 
     * 
     * @param string $name Event task name
     * @param array  $rec  Event task state
     * @param boolean  $flush  Flush task
     *  
     * @return void
     */
    public function setEventState($name, array $rec, $flush = true)
    {
        $this->setVar(static::EVENT_TASK_STATE_PREFIX . $name, $rec, $flush);
    }

    /**
     * Set event state
     *
     * @param string $name Event task name
     *
     * @return void
     */
    public function removeEventState($name)
    {
        $var = $this->findOneBy(array('name' => static::EVENT_TASK_STATE_PREFIX . $name));
        if ($var) {
            \XLite\Core\Database::getEM()->remove($var);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Check event state - finished or not
     *
     * @param string $name Event task name
     *
     * @return boolean
     */
    public function isFinishedEventState($name)
    {
        $record = $this->getEventState($name);

        return $record
            && ($record['state'] == \XLite\Core\EventTask::STATE_FINISHED || $record['state'] == \XLite\Core\EventTask::STATE_ABORTED);
    }

    /**
     * Check event state - finished or not
     *
     * @param string $name Event task name
     *
     * @return boolean
     */
    public function getEventStatePercent($name)
    {
        $record = $this->getEventState($name);

        $percent = 0;

        if ($record) {
            if ($this->isFinishedEventState($name)) {
                $percent = 100;

            } elseif (0 < $record['length']) {
                $percent = min(100, intval($record['position'] / $record['length'] * 100));
            }
        }

        return $percent;
    }


    // }}}
}

