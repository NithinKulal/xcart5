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
interface EntityVersionInterface
{
    public function getEntityVersion();

    public function setEntityVersion($uuid);
}