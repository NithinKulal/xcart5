<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Task
 *
 * @Entity
 * @Table (name="tasks")
 */
class Task extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Owner class
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $owner;

    /**
     * Trigger time
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $triggerTime = 0;

    /**
     * Task abstract data
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $data = array();

    /**
     * Owner instance
     *
     * @var \XLite\Core\Task\ATask
     */
    protected $ownerInstance;

    /**
     * Should we start the task
     *
     * @return boolean
     */
    public function isExpired()
    {
        \XLite\Core\Database::getEM()->refresh($this);
        return $this->getTriggerTime() < \XLite\Core\Converter::time()
            || $this->getTriggerTime() == 0;
    }

    /**
     * Get owner instance
     *
     * @return \XLite\Core\Task\ATask
     */
    public function getOwnerInstance()
    {
        if (!isset($this->ownerInstance)) {
            $class = $this->getOwner();
            if (\XLite\Core\Operator::isClassExists($class)) {
                $this->ownerInstance = new $class($this);
                if (!($this->ownerInstance instanceof \XLite\Core\Task\ATask)) {
                    $this->ownerInstance = false;
                }
            }
        }

        return $this->ownerInstance;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set owner
     *
     * @param string $owner
     * @return Task
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get owner
     *
     * @return string 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set triggerTime
     *
     * @param integer $triggerTime
     * @return Task
     */
    public function setTriggerTime($triggerTime)
    {
        $this->triggerTime = $triggerTime;
        return $this;
    }

    /**
     * Get triggerTime
     *
     * @return integer 
     */
    public function getTriggerTime()
    {
        return $this->triggerTime;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return Task
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }
}
