<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * DB-based temporary variables
 *
 * @Entity
 * @Table  (name="tmp_vars",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="name", columns={"name"})
 *      }
 * )
 */
class TmpVar extends \XLite\Model\AEntity
{
    /**
     * Option unique name
     *
     * @var string
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Name
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $name;

    /**
     * Value
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $value = '';

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
     * @return TmpVar
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
     * Set value
     *
     * @param text $value
     * @return TmpVar
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return text 
     */
    public function getValue()
    {
        return $this->value;
    }
}
