<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Session
 *
 * @Entity
 * @Table  (name="session_cells",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="iname", columns={"id", "name"})
 *      },
 *      indexes={
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class SessionCell extends \XLite\Model\AEntity
{
    /**
     * Cell unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $cell_id;

    /**
     * Name
     *
     * @var string
     *
     * @Column (type="string", length=255)
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
     * Value type
     *
     * @var string
     *
     * @Column (type="string", length=16)
     */
    protected $type;

    /**
     * @var \XLite\Model\Session
     *
     * @ManyToOne  (targetEntity="XLite\Model\Session", inversedBy="cells", cascade="persist")
     * @JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $session;

    /**
     * Automatically get variable type
     *
     * @param mixed $value Variable to check
     *
     * @return string
     */
    public static function getTypeByValue($value)
    {
        $type = gettype($value);

        return in_array($type, array('NULL', 'unknown type')) ? null : $type;
    }

    /**
     * Common getter
     *
     * NOTE: this function is designed as "static public" to use in repository
     * NOTE: customize this method instead of the "getValue()" one
     *
     * @param mixed  $value Value to prepare
     * @param string $type  Field type OPTIONAL
     *
     * @return mixed
     */
    public static function prepareValueForGet($value, $type = null)
    {
        $type = $type ?: static::getTypeByValue($value);

        switch ($type) {
            case 'boolean':
                $value = (bool) $value;
                break;

            case 'integer':
                $value = intval($value);
                break;

            case 'double':
                $value = doubleval($value);
                break;

            case 'string':
                $value = $value;
                break;

            case 'array':
            case 'object':
                $value = unserialize($value);
                break;

            default:
                $value = null;
        }

        return $value;
    }

    /**
     * Common setter
     *
     * NOTE: this function is designed as "static public" to use in repository
     * NOTE: customize this method instead of the "getValue()" one
     *
     * @param mixed  $value Value to prepare
     * @param string $type  Field type OPTIONAL
     *
     * @return mixed
     */
    public static function prepareValueForSet($value, $type = null)
    {
        $type = $type ?: static::getTypeByValue($value);

        switch ($type) {
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
                break;

            case 'array':
            case 'object':
                $value = serialize($value);
                break;

            default:
                $value = null;
        }

        return $value;
    }


    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return static::prepareValueForGet($this->value, $this->getType());
    }

    /**
     * Set value
     *
     * @param mixed $value Value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->type  = static::getTypeByValue($value);
        $this->value = static::prepareValueForSet($value, $this->type);
    }

    /**
     * Get cell_id
     *
     * @return integer 
     */
    public function getCellId()
    {
        return $this->cell_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SessionCell
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
     * Set type
     *
     * @param string $type
     * @return SessionCell
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
     * Set session
     *
     * @param \XLite\Model\Session $session
     * @return SessionCell
     */
    public function setSession(\XLite\Model\Session $session = null)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * Get session
     *
     * @return \XLite\Model\Session 
     */
    public function getSession()
    {
        return $this->session;
    }
}
