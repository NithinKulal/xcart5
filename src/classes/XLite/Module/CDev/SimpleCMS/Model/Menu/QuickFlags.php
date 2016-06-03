<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Model\Menu;

/**
 * Menu quick flags
 *
 * @Entity
 * @Table  (name="menu_quick_flags")
 */
class QuickFlags extends \XLite\Model\AEntity
{
    /**
     * Doctrine ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Total number of submenus
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $submenus_count_all = 0;

    /**
     * Number of enabled submenus
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $submenus_count_enabled = 0;

    /**
     * Relation to a menu entity
     *
     * @var \XLite\Module\CDev\SimpleCMS\Model\Menu
     *
     * @OneToOne   (targetEntity="XLite\Module\CDev\SimpleCMS\Model\Menu", inversedBy="quickFlags")
     * @JoinColumn (name="menu_id", referencedColumnName="id")
     */
    protected $menu;

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
     * Set submenus_count_all
     *
     * @param integer $submenusCountAll
     * @return QuickFlags
     */
    public function setSubmenusCountAll($submenusCountAll)
    {
        $this->submenus_count_all = $submenusCountAll;
        return $this;
    }

    /**
     * Get submenus_count_all
     *
     * @return integer 
     */
    public function getSubmenusCountAll()
    {
        return $this->submenus_count_all;
    }

    /**
     * Set submenus_count_enabled
     *
     * @param integer $submenusCountEnabled
     * @return QuickFlags
     */
    public function setSubmenusCountEnabled($submenusCountEnabled)
    {
        $this->submenus_count_enabled = $submenusCountEnabled;
        return $this;
    }

    /**
     * Get submenus_count_enabled
     *
     * @return integer 
     */
    public function getSubmenusCountEnabled()
    {
        return $this->submenus_count_enabled;
    }

    /**
     * Set menu
     *
     * @param \XLite\Module\CDev\SimpleCMS\Model\Menu $menu
     * @return QuickFlags
     */
    public function setMenu(\XLite\Module\CDev\SimpleCMS\Model\Menu $menu = null)
    {
        $this->menu = $menu;
        return $this;
    }

    /**
     * Get menu
     *
     * @return \XLite\Module\CDev\SimpleCMS\Model\Menu 
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
