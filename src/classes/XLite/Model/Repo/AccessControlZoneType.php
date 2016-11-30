<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Access control zone repo
 */
class AccessControlZoneType extends \XLite\Model\Repo\ARepo
{
    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('name'),
    );

    /**
     * @param $name
     *
     * @return null | \XLite\Model\AccessControlZoneType
     */
    public function getZoneByName($name)
    {
        foreach ($this->findAllTypes() as $zone) {
            if ($zone->checkIdentity($name)) {
                return $zone;
            }
        }

        return null;
    }

    /**
     * Find all Access control zone types
     *
     * @return array
     */
    public function findAllTypes()
    {
        $cnd = new \XLite\Core\CommonCell();
        return $this->search($cnd);
    }
}