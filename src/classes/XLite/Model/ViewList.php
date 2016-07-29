<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * View list
 *
 * @Entity
 * @Table  (name="view_lists",
 *          indexes={
 *              @Index (name="tl", columns={"tpl", "list"}),
 *              @Index (name="lzv", columns={"list", "zone", "version"})
 *          }
 * )
 * @HasLifecycleCallbacks
 */
class ViewList extends \XLite\Model\AEntity
{
    /**
     * Predefined weights
     */
    const POSITION_FIRST = 0;
    const POSITION_LAST  = 16777215;

    /**
     * Predefined interfaces
     */
    const INTERFACE_CUSTOMER = 'customer';
    const INTERFACE_ADMIN    = 'admin';
    const INTERFACE_CONSOLE  = 'console';
    const INTERFACE_MAIL     = 'mail';
    const INTERFACE_PDF      = 'pdf';

    /**
     * Version key 
     * 
     * @var string
     */
    protected static $versionKey;

    /**
     * List id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", length=11)
     */
    protected $list_id;

    /**
     * Class name
     *
     * @var string
     *
     * @Column (type="string", options={"charset"="latin1"})
     */
    protected $class = '';

    /**
     * Class list name
     *
     * @var string
     *
     * @Column (type="string", options={"charset"="latin1"})
     */
    protected $list;

    /**
     * List interface
     *
     * @var string
     *
     * @Column (type="string", length=16, options={"charset"="latin1"})
     */
    protected $zone = self::INTERFACE_CUSTOMER;

    /**
     * Child class name
     *
     * @var string
     *
     * @Column (type="string", length=512, options={"charset"="latin1"})
     */
    protected $child = '';

    /**
     * Child weight
     *
     * @var integer
     *
     * @Column (type="integer", length=11)
     */
    protected $weight = 0;

    /**
     * Template relative path
     *
     * @var string
     *
     * @Column (type="string", length=512, options={"charset"="latin1"})
     */
    protected $tpl = '';

    /**
     * Template relative path
     *
     * @var string
     *
     * @Column (type="string", length=32, nullable=true)
     */
    protected $version;

    /**
     * Get in zone hash
     *
     * @return string
     */
    public function getHashWithoutZone()
    {
        $prefix = \XLite::COMMON_INTERFACE . '/';
        $pattern = '/^' . preg_quote($prefix, '/') . '/uS';

        $hashValues = [
            $this->getClass(),
            $this->getList(),
            $this->getChild(),
            $this->getWeight(),
            preg_replace($pattern, '', $this->getTpl()),
        ];

        return md5(serialize($hashValues));
    }

    /**
     * Set version key 
     * 
     * @param string $key Key
     *  
     * @return void
     */
    public static function setVersionKey($key)
    {
        static::$versionKey = $key;
    }

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @PrePersist
     */
    public function prepareBeforeCreate()
    {
        if (static::$versionKey && !$this->getVersion()) {
            $this->setVersion(static::$versionKey);
        }        
    }


    /**
     * Get list_id
     *
     * @return integer 
     */
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * Set class
     *
     * @param string $class
     * @return ViewList
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
     * Set list
     *
     * @param string $list
     * @return ViewList
     */
    public function setList($list)
    {
        $this->list = $list;
        return $this;
    }

    /**
     * Get list
     *
     * @return string 
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Set zone
     *
     * @param string $zone
     * @return ViewList
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * Get zone
     *
     * @return string 
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set child
     *
     * @param string $child
     * @return ViewList
     */
    public function setChild($child)
    {
        $this->child = $child;
        return $this;
    }

    /**
     * Get child
     *
     * @return string 
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return ViewList
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

    /**
     * Set tpl
     *
     * @param string $tpl
     * @return ViewList
     */
    public function setTpl($tpl)
    {
        $this->tpl = $tpl;
        return $this;
    }

    /**
     * Get tpl
     *
     * @return string 
     */
    public function getTpl()
    {
        return $this->tpl;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return ViewList
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }
}
