<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Access control entity
 *
 * @Entity
 * @Table  (name="access_control_entities")
 */
class AccessControlEntity extends \XLite\Model\AEntity
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
     * Unique entity identifier
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $uid;

    /**
     * Entity type
     *
     * @var \XLite\Model\AccessControlEntityType
     *
     * @ManyToOne  (targetEntity="\XLite\Model\AccessControlEntityType")
     * @JoinColumn (name="type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $type;

    /**
     * Cell
     *
     * @var \XLite\Model\AccessControlCell
     *
     * @ManyToOne  (targetEntity="\XLite\Model\AccessControlCell", inversedBy="access_control_entities")
     * @JoinColumn (name="cell_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $cell;

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
     * Return type
     *
     * @return AccessControlEntityType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Return entity unique identifier
     *
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set entity unique identifier
     *
     * @param $uid
     *
     * @return $this
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Return access control cell
     *
     * @return AccessControlCell
     */
    public function getCell()
    {
        return $this->cell;
    }
    
    /**
     * Set access control cell
     *
     * @param $cell
     *
     * @return $this
     */
    public function setCell($cell)
    {
        $this->cell = $cell;
        return $this;
    }

    /**
     * Return entity
     *
     * @return null|AEntity
     */
    public function getEntity()
    {
        $repo = \XLite\Core\Database::getRepo($this->getType()->getType());

        if ($repo) {
            return $repo->find($this->getUid());
        }

        return null;
    }
    
    /**
     * Map entity to access control cell
     *
     * @param \XLite\Model\AEntity $entity
     * @param string | null | \XLite\Model\AccessControlEntityType $type
     *
     * @return $this
     *
     * @throws \XLite\Core\Exception\AccessControlEntityTypeNotFoundException
     */
    public function setEntity(\XLite\Model\AEntity $entity, $type = null)
    {
        if ($entity instanceof \XLite\Model\AccessControlEntity) {
            $entity = $entity->getEntity();
        }

        if (!($type instanceof \XLite\Model\AccessControlEntityType)) {
            if (null === $type) {
                $types = \XLite\Core\Database::getRepo('\XLite\Model\AccessControlEntityType')->findAllTypes();

                foreach ($types as $entityType) {
                    if ($entityType->checkType($entity)) {
                        $type = $entityType;
                        break;
                    }
                }
            } else {
                $type = \XLite\Core\Database::getRepo('\XLite\Model\AccessControlEntityType')->findByType($type);
            }
        }

        if (!($type instanceof \XLite\Model\AccessControlEntityType)) {
            $entityClass = get_class($entity);
            throw new \XLite\Core\Exception\AccessControlEntityTypeNotFoundException("Access control entity type for entity \"{$entityClass}\" not found.");
        } else {
            $this->setType($type)
                ->setUid($entity->getUniqueIdentifier());
        }

        return $this;
    }

    /**
     * Check entity type & access control entity type is match
     *
     * @param AEntity $entity
     *
     * @return mixed
     */
    public function checkType(\XLite\Model\AEntity $entity)
    {
        return $this->getType()->checkType($entity);
    }

    /**
     * Check if string equal with type
     *
     * @param string $type
     *
     * @return mixed
     */
    public function checkStringType($type)
    {
        return $this->getType()->checkStringType($type);
    }

    /**
     * Check entity uid & access control entity uid is match
     *
     * @param AEntity $entity
     *
     * @return mixed
     */
    public function checkIdentity(\XLite\Model\AEntity $entity)
    {
        return $this->getType()->checkType($entity) && $this->getUid() === $entity->getUniqueIdentifier();
    }
}