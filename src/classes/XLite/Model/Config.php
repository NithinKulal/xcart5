<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * DB-based configuration registry
 *
 * @Entity
 * @Table  (name="config",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="nc", columns={"name", "category"})
 *      },
 *      indexes={
 *          @Index (name="orderby", columns={"orderby"}),
 *          @Index (name="type", columns={"type"})
 *      }
 * )
 */
class Config extends \XLite\Model\Base\I18n
{
    /**
     * Name for the Shipping category options
     */
    const SHIPPING_CATEGORY = 'Shipping';

    /**
     * Prefix for the shipping values
     */
    const SHIPPING_VALUES_PREFIX = 'anonymous_';

    /**
     * Option unique name
     *
     * @var string
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer")
     */
    protected $config_id;

    /**
     * Option name
     *
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $name;

    /**
     * Option category
     *
     * @var string
     *
     * @Column (type="string", length=64)
     */
    protected $category;

    /**
     * Option type
     * Allowed values:'','text','textarea','checkbox','country','state','select','serialized','separator'
     *     or form field class name
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $type = '';

    /**
     * Option position within category
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $orderby = 0;

    /**
     * Option value
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $value = '';

    /**
     * New value temporary field
     *
     * @var string
     */
    protected $newValue;

    /**
     * Widget parameters
     *
     * @var array
     *
     * @Column (type="array", nullable=true)
     */
    protected $widgetParameters;

    /**
     * Set new value
     *
     * @param string $value Value
     *
     * @return void
     */
    public function setNewValue($value)
    {
        $this->newValue = $value;
    }

    /**
     * Returns new value
     *
     * @return string
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * Get config_id
     *
     * @return integer 
     */
    public function getConfigId()
    {
        return $this->config_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Config
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
     * Set category
     *
     * @param string $category
     * @return Config
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Config
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set orderby
     *
     * @param integer $orderby
     * @return Config
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Get orderby
     *
     * @return integer 
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set value
     *
     * @param text $value
     * @return Config
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

    /**
     * Set widgetParameters
     *
     * @param array $widgetParameters
     * @return Config
     */
    public function setWidgetParameters($widgetParameters)
    {
        $this->widgetParameters = $widgetParameters;
        return $this;
    }

    /**
     * Get widgetParameters
     *
     * @return array 
     */
    public function getWidgetParameters()
    {
        return $this->widgetParameters;
    }
}
