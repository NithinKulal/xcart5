<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model\EntityVersion;

/**
 * Entities implementing EntityTypeInterface will have their entity version field changed automatically on every update.
 * Use EntityVersionTrait to add actual implementation.
 */
trait EntityVersionTrait
{
    /**
     * Entity version
     *
     * @var string
     *
     * @Column (type="guid")
     */
    protected $entityVersion;

    public function getEntityVersion()
    {
        return $this->entityVersion;
    }

    public function setEntityVersion($uuid)
    {
        $this->entityVersion = $uuid;
    }
}