<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Access control cell zone
 *
 * @Entity
 * @Table  (name="access_control_zones",
 *          uniqueConstraints={
 *              @UniqueConstraint (name="cz", columns={"cell_id", "type_id"})
 *          })
 */
class AccessControlZone extends \XLite\Model\AEntity
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
     * Cell
     *
     * @var \XLite\Model\AccessControlCell
     *
     * @ManyToOne  (targetEntity="\XLite\Model\AccessControlCell", inversedBy="access_control_zones")
     * @JoinColumn (name="cell_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $cell;

    /**
     * Zone
     *
     * @var \XLite\Model\AccessControlZoneType
     *
     * @ManyToOne  (targetEntity="\XLite\Model\AccessControlZoneType")
     * @JoinColumn (name="type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $type;

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
     * Return Cell
     *
     * @return AccessControlCell
     */
    public function getCell()
    {
        return $this->cell;
    }

    /**
     * Set Cell
     *
     * @param AccessControlCell $cell
     *
     * @return $this
     */
    public function setCell($cell)
    {
        $this->cell = $cell;
        return $this;
    }

    /**
     * Return Zone Type
     *
     * @return AccessControlZoneType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Zone Type
     *
     * @param AccessControlZoneType $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set Zone by zone name
     *
     * @param string $name
     *
     * @return $this
     * @throws \XLite\Core\Exception\AccessControlZoneTypeNotFoundException
     */
    public function setTypeByName($name)
    {
        if ($zone = \XLite\Core\Database::getRepo('\XLite\Model\AccessControlZoneType')->getZoneByName($name)) {
            $this->type = $zone;
        } else {
            throw new \XLite\Core\Exception\AccessControlZoneTypeNotFoundException("Access control zone for name \"{$name}\" not found.");
        }

        return $this;
    }

    /**
     * Set Zone by zone
     *
     * @param \XLite\Model\AccessControlZone $zone
     *
     * @return $this
     */
    public function setTypeByZone(\XLite\Model\AccessControlZone $zone)
    {
        $this->type = $zone->getType();

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
        return $this->getType()->checkIdentity($zone);
    }
}