<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Measure
 *
 * @Entity
 * @Table  (name="measures")
 */
class Measure extends \XLite\Model\AEntity
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Date (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $date;

    /**
     * File system test : time (msec.)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $fsTime;

    /**
     * Database test : time (msec.)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $dbTime;

    /**
     * Camputation test : time (msec.)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $cpuTime;

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
     * Set date
     *
     * @param integer $date
     * @return Measure
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set fsTime
     *
     * @param integer $fsTime
     * @return Measure
     */
    public function setFsTime($fsTime)
    {
        $this->fsTime = $fsTime;
        return $this;
    }

    /**
     * Get fsTime
     *
     * @return integer 
     */
    public function getFsTime()
    {
        return $this->fsTime;
    }

    /**
     * Set dbTime
     *
     * @param integer $dbTime
     * @return Measure
     */
    public function setDbTime($dbTime)
    {
        $this->dbTime = $dbTime;
        return $this;
    }

    /**
     * Get dbTime
     *
     * @return integer 
     */
    public function getDbTime()
    {
        return $this->dbTime;
    }

    /**
     * Set cpuTime
     *
     * @param integer $cpuTime
     * @return Measure
     */
    public function setCpuTime($cpuTime)
    {
        $this->cpuTime = $cpuTime;
        return $this;
    }

    /**
     * Get cpuTime
     *
     * @return integer 
     */
    public function getCpuTime()
    {
        return $this->cpuTime;
    }
}
