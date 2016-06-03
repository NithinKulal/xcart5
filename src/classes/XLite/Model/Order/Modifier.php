<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order;

/**
 * Order modifier
 *
 * @Entity
 * @Table (name="order_modifiers")
 */
class Modifier extends \XLite\Model\AEntity
{
    /**
     * ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Logic class name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $class;

    /**
     * Weight
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $weight = 0;

    /**
     * Modifier object (cache)
     *
     * @var \XLite\Logic\Order\Modifier\AModifier
     */
    protected $modifier;

    /**
     * Magic call
     *
     * @param string $method Method name
     * @param array  $args   Arguments list OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $args = array())
    {
        $modifier = $this->getModifier();

        return ($modifier && method_exists($modifier, $method))
            ? call_user_func_array(array($modifier, $method), $args)
            : parent::__call($method, $args);
    }

    /**
     * Get modifier object
     *
     * @return \XLite\Logic\Order\Modifier\AModifier
     */
    public function getModifier()
    {
        if (!isset($this->modifier) && \XLite\Core\Operator::isClassExists($this->getClass())) {
            $class = $this->getClass();
            $this->modifier = new $class($this);
        }

        return $this->modifier;
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
     * Set class
     *
     * @param string $class
     * @return Modifier
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Modifier
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }
}
