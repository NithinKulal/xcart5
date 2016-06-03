<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Entity type version is a UUID that changes every time an entity of the given type is persisted/updated/removed
 *
 * @Entity (repositoryClass="XLite\Model\Repo\EntityTypeVersion")
 * @Table (name="entity_type_versions")
 */
class EntityTypeVersion
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Entity FQCN
     *
     * @var string
     *
     * @Column (type="string", length=255, unique=true)
     */
    protected $entityType;

    /**
     * Entity type version
     *
     * @var string
     *
     * @Column (type="guid")
     */
    protected $version;

    public function __construct($entityType, $version)
    {
        $this->entityType = $entityType;
        $this->version    = $version;
    }
}
