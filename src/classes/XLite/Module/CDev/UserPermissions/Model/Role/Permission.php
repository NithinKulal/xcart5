<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\UserPermissions\Model\Role;

/**
 * Role permission
 */
abstract class Permission extends \XLite\Model\Role\Permission implements \XLite\Base\IDecorator
{
    /**
     * Enabled
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;


    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Permission
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}

