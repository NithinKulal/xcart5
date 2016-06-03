<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Membership
 *
 * @Entity
 * @Table (name="notifications")
 */
class Notification extends \XLite\Model\Base\I18n
{
    /**
     * Notification templates directory
     *
     * @var string
     *
     * @Id
     * @Column (type="string", length=255, unique=true)
     */
    protected $templatesDirectory = '';

    /**
     * Is available for admin
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $availableForAdmin = false;

    /**
     * Is available for admin
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $availableForCustomer = false;

    /**
     * Is available for admin
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabledForAdmin = false;

    /**
     * Is available for admin
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabledForCustomer = false;

    /**
     * Set templatesDirectory
     *
     * @param string $templatesDirectory
     * @return Notification
     */
    public function setTemplatesDirectory($templatesDirectory)
    {
        $this->templatesDirectory = $templatesDirectory;
        return $this;
    }

    /**
     * Get templatesDirectory
     *
     * @return string 
     */
    public function getTemplatesDirectory()
    {
        return $this->templatesDirectory;
    }

    /**
     * Set availableForAdmin
     *
     * @param boolean $availableForAdmin
     * @return Notification
     */
    public function setAvailableForAdmin($availableForAdmin)
    {
        $this->availableForAdmin = $availableForAdmin;
        return $this;
    }

    /**
     * Get availableForAdmin
     *
     * @return boolean 
     */
    public function getAvailableForAdmin()
    {
        return $this->availableForAdmin;
    }

    /**
     * Set availableForCustomer
     *
     * @param boolean $availableForCustomer
     * @return Notification
     */
    public function setAvailableForCustomer($availableForCustomer)
    {
        $this->availableForCustomer = $availableForCustomer;
        return $this;
    }

    /**
     * Get availableForCustomer
     *
     * @return boolean 
     */
    public function getAvailableForCustomer()
    {
        return $this->availableForCustomer;
    }

    /**
     * Set enabledForAdmin
     *
     * @param boolean $enabledForAdmin
     * @return Notification
     */
    public function setEnabledForAdmin($enabledForAdmin)
    {
        $this->enabledForAdmin = $enabledForAdmin;
        return $this;
    }

    /**
     * Get enabledForAdmin
     *
     * @return boolean 
     */
    public function getEnabledForAdmin()
    {
        return $this->enabledForAdmin;
    }

    /**
     * Set enabledForCustomer
     *
     * @param boolean $enabledForCustomer
     * @return Notification
     */
    public function setEnabledForCustomer($enabledForCustomer)
    {
        $this->enabledForCustomer = $enabledForCustomer;
        return $this;
    }

    /**
     * Get enabledForCustomer
     *
     * @return boolean 
     */
    public function getEnabledForCustomer()
    {
        return $this->enabledForCustomer;
    }
}
