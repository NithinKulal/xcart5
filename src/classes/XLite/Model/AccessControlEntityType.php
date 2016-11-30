<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Access control entity type
 *
 * @Entity
 * @Table  (name="access_control_entity_types",
 *          uniqueConstraints={
 *              @UniqueConstraint (name="type", columns={"type"})
 *          },
 *          indexes={
 *              @Index (name="type", columns={"type"})
 *          }
 *     )
 */
class AccessControlEntityType extends \XLite\Model\AEntity
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
     * Entity class
     *
     * @var string
     *
     * @Column (type="string")
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
     * Return type
     *
     * @return string
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
        if (0 !== strpos($type, '\\')) {
            $type = '\\' . $type;
        }

        $this->type = $type;
        return $this;
    }

    /**
     * Check if entity type is suitable with this type
     *
     * @param AEntity $entity
     *
     * @return mixed
     */
    public function checkType(\XLite\Model\AEntity $entity)
    {
        $type = $this->getType();

        return $entity instanceof $type;
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
        if (0 !== strpos($type, '\\')) {
            $type = '\\' . $type;
        }

        return $type === $this->getType();
    }
}