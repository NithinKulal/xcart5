<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Access control zone
 *
 * @Entity
 * @Table  (name="access_control_zone_types",
 *          uniqueConstraints={
 *              @UniqueConstraint (name="name", columns={"name"})
 *          },
 *          indexes={
 *              @Index (name="name", columns={"name"})
 *          })
 */
class AccessControlZoneType extends \XLite\Model\AEntity
{
    const ZONE_TYPE_ORDER = 'order';
    
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
     * Zone name
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $name;

    /**
     * Return Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Check if zone name match parameter
     *
     * @param string $zone
     *
     * @return mixed
     */
    public function checkIdentity($zone)
    {
        return $this->getName() === $zone;
    }
}