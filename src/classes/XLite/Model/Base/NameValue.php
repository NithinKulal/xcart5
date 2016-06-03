<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

/**
 * Name-value abstract storage
 *
 * @MappedSuperclass
 */
abstract class NameValue extends \XLite\Model\AEntity
{

    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Parameter name 
     * 
     * @var string
     *
     * @Column (type="string")
     */
    protected $name;

    /**
     * Semi-serialized parameter value representation
     * 
     * @var string
     *
     * @Column (type="text")
     */
    protected $value;

    /**
     * Get parameter value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = @unserialize($this->value);

        return false === $value ? $this->value : $value;
    }

    /**
     * Set parameter value
     *
     * @param mixed $value Parameter value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = is_scalar($value) ? $value : serialize($value);
    }
}
