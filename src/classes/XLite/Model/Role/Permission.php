<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Role;

/**
 * Permission
 *
 * @Entity
 * @Table (name="permissions")
 */
class Permission extends \XLite\Model\Base\I18n
{
    const ROOT_ACCESS = 'root access';

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
     * Code
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=32)
     */
    protected $code;

    /**
     * Section
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $section;

    /**
     * Roles
     *
     * @var \XLite\Model\Role
     *
     * @ManyToMany (targetEntity="XLite\Model\Role", inversedBy="permissions")
     * @JoinTable (
     *      name="role_permissions",
     *      joinColumns={@JoinColumn (name="permission_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="role_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $roles;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get public name
     *
     * @return string
     */
    public function getPublicName()
    {
        return $this->getName() ?: $this->getCode();
    }

    /**
     * Use this method to check if the given permission code allows with the permission
     *
     * @param string $code Code
     *
     * @return boolean
     */
    public function isAllowed($code)
    {
        return in_array($this->getCode(), array(static::ROOT_ACCESS, $code), true);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Permission
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set section
     *
     * @param string $section
     * @return Permission
     */
    public function setSection($section)
    {
        $this->section = $section;
        return $this;
    }

    /**
     * Get section
     *
     * @return string 
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Add roles
     *
     * @param \XLite\Model\Role $roles
     * @return Permission
     */
    public function addRoles(\XLite\Model\Role $roles)
    {
        $this->roles[] = $roles;
        return $this;
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
