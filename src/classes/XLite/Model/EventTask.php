<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Event task
 *
 * @Entity
 * @Table (name="event_tasks")
 */
class EventTask extends \XLite\Model\AEntity
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
     * Event name
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $name;

    /**
     * Event narguments
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $arguments = array();

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
     * Set name
     *
     * @param string $name
     * @return EventTask
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set arguments
     *
     * @param array $arguments
     * @return EventTask
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * Get arguments
     *
     * @return array 
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
