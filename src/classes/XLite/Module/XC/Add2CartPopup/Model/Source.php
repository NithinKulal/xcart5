<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\Model;

/**
 * Fake entity to use for products sources items list
 *
 * @Entity
 * @Table  (name="add2cart_popup_sources")
 */
class Source extends \XLite\Model\AEntity
{
    /**
     * Unique Id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Unique source code
     *
     * @var string
     *
     * @Column (type="string", length=3)
     */
    protected $code;

    /**
     * Enabled
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled;

    /**
     * Position (priority)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position;


    /**
     * isPersistens is always returns true for this object as this is a fake entity
     *
     * @return boolean
     */
    public function isPersistent()
    {
        return true;
    }

    /**
     * Get entity unique identifier value
     *
     * @return string
     */
    public function getUniqueIdentifier()
    {
        return $this->code;
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
     * Set code
     *
     * @param string $code
     * @return Source
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Source
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Source
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
}
