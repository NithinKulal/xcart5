<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Model;

/**
 * Menu
 *
 * @Entity
 * @Table  (name="menus",
 *      indexes={
 *          @Index (name="enabled", columns={"enabled", "type"}),
 *          @Index (name="position", columns={"position"})
 *      }
 * )
 */
class Menu extends \XLite\Model\Base\I18n
{
    /**
     * Menu types
     */
    const MENU_TYPE_PRIMARY = 'P';
    const MENU_TYPE_FOOTER  = 'F';

    const DEFAULT_HOME_PAGE = '{home}';
    const DEFAULT_MY_ACCOUNT = '{my account}';

    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Link
     *
     * @var   string
     *
     * @Column (type="string", nullable=true)
     */
    protected $link;

    /**
     * Node left value
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $lpos = 0;

    /**
     * Node right value
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $rpos = 0;

    /**
     * Menu "depth" in the tree
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $depth = -1;

    /**
     * Some cached flags
     *
     * @var \XLite\Module\CDev\SimpleCMS\Model\Menu\QuickFlags
     *
     * @OneToOne (targetEntity="XLite\Module\CDev\SimpleCMS\Model\Menu\QuickFlags", mappedBy="menu", cascade={"all"})
     */
    protected $quickFlags;

    /**
     * Child menus 
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\SimpleCMS\Model\Menu", mappedBy="parent", cascade={"all"})
     * @OrderBy ({"id"="ASC"})
     */
    protected $children;

    /**
     * Parent menu
     *
     * @var \XLite\Module\CDev\SimpleCMS\Model\Menu
     *
     * @ManyToOne  (targetEntity="XLite\Module\CDev\SimpleCMS\Model\Menu", inversedBy="children")
     * @JoinColumn (name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * Type
     *
     * @var string
     *
     * @Column (type="string", length=1)
     */
    protected $type;

    /**
     * Position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Is menu enabled or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Visible for anonymous only (A), logged in only (L), for all visitors (AL)
     *
     * @var string
     *
     * @Column (type="string", length=2)
     */
    protected $visibleFor = 'AL';


    /**
     * Get menu types
     *
     * @return array
     */
    public static function getTypes()
    {
        return array(
            static::MENU_TYPE_PRIMARY => 'Primary menu',
            static::MENU_TYPE_FOOTER  => 'Footer menu',
        );
    }

    /**
     * Set type
     *
     * @param string $type Type
     *
     * @return void
     */
    public function setType($type)
    {
        $types = static::getTypes();

        if (isset($types[$type])) {
            $this->type = $type;
        }
    }

    /**
     * Defines the resulting link values for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkURLs()
    {
        return array(
            static::DEFAULT_HOME_PAGE   => ' ',
            static::DEFAULT_MY_ACCOUNT  => '?target=order_list',
        );
    }

    /**
     * Defines the link controller class names for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkControllers()
    {
        return array(
            static::DEFAULT_HOME_PAGE => '\XLite\Controller\Customer\Main',
            static::DEFAULT_MY_ACCOUNT => array(
                '\XLite\Controller\Customer\OrderList',
                '\XLite\Controller\Customer\AddressBook',
                '\XLite\Controller\Customer\Profile',
            ),
        );
    }

    /**
     * Link value for home page is defined in static::DEFAULT_HOME_PAGE constant
     *
     * @see static::DEFAULT_HOME_PAGE
     *
     * @return string
     */
    public function getURL()
    {
        $list = $this->defineLinkURLs();
        $link = $this->getLink();

        return isset($list[$link]) ? $list[$link] : $link;
    }

    /**
     * Defines the link controller class name
     * or FALSE if there is no specific link value
     *
     * @return string | false
     */
    public function getLinkController()
    {
        $list = $this->defineLinkControllers();
        $link = $this->getLink();

        return isset($list[$link]) ? $list[$link] : false;
    }

    /**
     * Get object unique id
     *
     * @return integer
     */
    public function getMenuId()
    {
        return $this->id;
    }

    /**
     * Translation getter. AUTOGENERATED
     *
     * @return string
     */
    public function getName()
    {
        return $this->getSoftTranslation()->getName();
    }

    /**
     * Get depth
     *
     * @return string
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set parent
     *
     * @param \XLite\Module\CDev\SimpleCMS\Model\Menu $parent Parent menu OPTIONAL
     *
     * @return void
     */
    public function setParent(\XLite\Module\CDev\SimpleCMS\Model\Menu $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Return parent menu ID
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->getParent() ? $this->getParent()->getMenuId() : 0;
    }

    /**
     * Return submenus list
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubmenus()
    {
        return $this->getChildren();
    }

    /**
     * Get menu path
     *
     * @return array
     */
    public function getPath()
    {
        return $this->getRepository()->getMenuPath($this->getMenuId());
    }

    /**
     * Get the number of submenus
     *
     * @return integer
     */
    public function getSubmenusCount()
    {
        $result = 0;

        $enabledCondition = $this->getRepository()->getEnabledCondition();
        $quickFlags = $this->getQuickFlags();

        if ($quickFlags) {
            $result = $enabledCondition
                ? $quickFlags->getSubmenusCountEnabled()
                : $quickFlags->getSubmenusCountAll();
        }

        return $result;
    }

    /**
     * Get the number of submenus
     *
     * @return integer
     */
    public function getSubmenusCountConditional()
    {
        $currentState = \XLite\Core\Auth::getInstance()->isLogged() && !\XLite\Core\Auth::getInstance()->isAnonymous()
            ? 'L'
            : 'A';

        return $this->getSubmenus()->filter(function(\XLite\Module\CDev\SimpleCMS\Model\Menu $submenu) use ($currentState){
            return $submenu->getEnabled() && ($submenu->getVisibleFor() === 'AL' || $submenu->getVisibleFor() === $currentState);
        })->count();
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
     * Set link
     *
     * @param string $link
     * @return Menu
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Set lpos
     *
     * @param integer $lpos
     * @return Menu
     */
    public function setLpos($lpos)
    {
        $this->lpos = $lpos;
        return $this;
    }

    /**
     * Get lpos
     *
     * @return integer 
     */
    public function getLpos()
    {
        return $this->lpos;
    }

    /**
     * Set rpos
     *
     * @param integer $rpos
     * @return Menu
     */
    public function setRpos($rpos)
    {
        $this->rpos = $rpos;
        return $this;
    }

    /**
     * Get rpos
     *
     * @return integer 
     */
    public function getRpos()
    {
        return $this->rpos;
    }

    /**
     * Set depth
     *
     * @param integer $depth
     * @return Menu
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Menu
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Menu
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

    /**
     * Set visibleFor
     *
     * @param string $visibleFor
     * @return Menu
     */
    public function setVisibleFor($visibleFor)
    {
        $this->visibleFor = $visibleFor;
        return $this;
    }

    /**
     * Get visibleFor
     *
     * @return string 
     */
    public function getVisibleFor()
    {
        return $this->visibleFor;
    }

    /**
     * Set quickFlags
     *
     * @param \XLite\Module\CDev\SimpleCMS\Model\Menu\QuickFlags $quickFlags
     * @return Menu
     */
    public function setQuickFlags(\XLite\Module\CDev\SimpleCMS\Model\Menu\QuickFlags $quickFlags = null)
    {
        $this->quickFlags = $quickFlags;
        return $this;
    }

    /**
     * Get quickFlags
     *
     * @return \XLite\Module\CDev\SimpleCMS\Model\Menu\QuickFlags 
     */
    public function getQuickFlags()
    {
        return $this->quickFlags;
    }

    /**
     * Add children
     *
     * @param \XLite\Module\CDev\SimpleCMS\Model\Menu $children
     * @return Menu
     */
    public function addChildren(\XLite\Module\CDev\SimpleCMS\Model\Menu $children)
    {
        $this->children[] = $children;
        return $this;
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get parent
     *
     * @return \XLite\Module\CDev\SimpleCMS\Model\Menu 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
