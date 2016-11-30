<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Access control cell
 *
 * @Entity
 * @Table  (name="access_control_cells",
 *          uniqueConstraints={
 *              @UniqueConstraint (name="hash", columns={"hash"})
 *          },
 *          indexes={
 *              @Index (name="hash", columns={"hash"})
 *          })
 * @HasLifecycleCallbacks
 */
class AccessControlCell extends \XLite\Model\AEntity
{
    const ACCESS_CONTROL_CELL_AVAILABILITY_TTL = 3600;

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
     * Cell hash
     *
     * @var string
     *
     * @Column (type="string", nullable=true)
     */
    protected $hash;

    /**
     * Attributes
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="\XLite\Model\AccessControlEntity", mappedBy="cell", cascade={"all"})
     */
    protected $access_control_entities;

    /**
     * Attributes
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="\XLite\Model\AccessControlZone", mappedBy="cell", cascade={"all"})
     */
    protected $access_control_zones;

    /**
     * Cell hash
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $returnData;

    /**
     * Method that executes on resend action
     *
     * @var string
     *
     * @Column (type="string", nullable=true)
     */
    protected $resendMethod = null;

    /**
     * Creation date (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date = 0;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->access_control_entities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->access_control_zones = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
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
        $time = \XLite\Core\Converter::time();

        if (!$this->getDate()) {
            $this->setDate($time);
        }
    }

    /**
     * Return id
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     * 
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return hash
     * 
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set hash
     * 
     * @param $hash
     *
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Return access control entities
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAccessControlEntities()
    {
        return $this->access_control_entities;
    }

    /**
     * Return one access control entity for type
     *
     * @param string $type
     *
     * @return \XLite\Model\AccessControlEntity | null
     */
    public function getAccessControlEntityByType($type = null)
    {
        foreach ($this->getAccessControlEntities() as $ace) {
            if ($ace->checkStringType($type)) {
                return $ace;
            }
        }
        
        return null;
    }

    /**
     * Set access control entities
     * 
     * @param \XLite\Model\AccessControlEntity[] $accessControlEntities
     *
     * @return $this
     */
    public function setAccessControlEntities($accessControlEntities)
    {
        $this->access_control_entities = $accessControlEntities;
        return $this;
    }

    /**
     * Add access control entity
     *
     * @param \XLite\Model\AccessControlEntity $accessControlEntity
     * @return $this
     */
    public function addAccessControlEntity(\XLite\Model\AccessControlEntity $accessControlEntity)
    {
        $this->access_control_entities[] = $accessControlEntity;
        return $this;
    }

    /**
     * Return access control zones
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAccessControlZones()
    {
        return $this->access_control_zones;
    }

    /**
     * Set access control zones
     * 
     * @param \XLite\Model\AccessControlZone[] $accessControlZones
     *
     * @return $this
     */
    public function setAccessControlZones($accessControlZones)
    {
        $this->access_control_zones = $accessControlZones;
        return $this;
    }

    /**
     * Add access control entity
     *
     * @param \XLite\Model\AccessControlZone $accessControlZone
     * 
*@return $this
     */
    public function addAccessControlZone(\XLite\Model\AccessControlZone $accessControlZone)
    {
        $this->access_control_zones[] = $accessControlZone;
        return $this;
    }

    /**
     * Get return data
     * 
     * @return array
     */
    public function getReturnData()
    {
        $result = @unserialize($this->returnData);
        return is_array($result) ? $result : [];
    }

    /**
     * Set return data
     * 
     * @param array $returnData
     */
    public function setReturnData(array $returnData)
    {
        $this->returnData = serialize($returnData);
    }

    /**
     * Merge param into return data
     *
     * @param array $returnData
     */
    public function mergeReturnData(array $returnData)
    {
        $this->setReturnData(array_merge(
            $this->getReturnData(),
            is_array($returnData) ? $returnData : []
        ));
    }

    /**
     * Return ResendMethod
     *
     * @return string
     */
    public function getResendMethod()
    {
        return $this->resendMethod;
    }

    /**
     * Set ResendMethod
     *
     * @param string $resendMethod
     *
     * @return $this
     */
    public function setResendMethod($resendMethod)
    {
        $this->resendMethod = $resendMethod;
        return $this;
    }

    /**
     * Return date timestamp
     * 
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     * 
     * @param int $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Check is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return (\XLite\Core\Converter::time() - static::ACCESS_CONTROL_CELL_AVAILABILITY_TTL) > $this->getDate();
    }

    /**
     * Return true if cell has access to entity
     *
     * @param AEntity $entity
     *
     * @return bool
     */
    public function hasEntityAccess(\XLite\Model\AEntity $entity)
    {
        foreach ($this->getAccessControlEntities() as $accessControlEntity) {
            if ($accessControlEntity->checkIdentity($entity)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Return true if cell has access to entity
     *
     * @param string $zone
     *
     * @return bool
     */
    public function hasZoneAccess($zone)
    {
        foreach ($this->getAccessControlZones() as $accessControlZone) {
            if ($accessControlZone->checkIdentity($zone)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return URL
     *
     * @return string
     */
    public function buildReturnURL()
    {
        $data = $this->getReturnData();

        if (!is_array($data)) {
            $data = [];
        }

        return \XLite\Core\Converter::buildFullURL(
            isset($data['target']) ? $data['target'] : '',
            isset($data['action']) ? $data['action'] : '',
            isset($data['params']) ? $data['params'] : []
        );
    }
}