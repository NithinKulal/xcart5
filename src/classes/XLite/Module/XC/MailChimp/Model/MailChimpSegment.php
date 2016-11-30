<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;

use \XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp mail list
 *
 * @Entity (repositoryClass="\XLite\Module\XC\MailChimp\Model\Repo\MailChimpSegment")
 * @Table  (name="mailchimp_list_segments")
 */
class MailChimpSegment extends \XLite\Model\AEntity
{
    /**
     * List ID
     *
     * @var string
     *
     * @Id
     * @Column (type="string", length=32)
     */
    protected $id = '';

    /**
     * List name
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $name = '';

    /**
     * List creation date
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $created_date = '';

    /**
     * Is static MailChimp segment
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $static = false;

    /**
     * MailChimp parent list
     *
     * @var \XLite\Module\XC\MailChimp\Model\MailChimpList
     *
     * @ManyToOne (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpList", inversedBy="segments")
     */
    protected $list;

    /**
     * Use amount of orders as condition
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $useOrdersLastMonth = false;

    /**
     * Amount of orders last month
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $ordersLastMonth = 0;

    /**
     * Use total amount of all orders as condition
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $useOrderAmount = false;

    /**
     * Total for all orders
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $orderAmount = 0.0;

    /**
     * Use memberships as condition
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $useMemberships = false;

    /**
     * Memberships
     *
     * @var \XLite\Model\Membership[]
     *
     * @ManyToMany (targetEntity="\XLite\Model\Membership")
     * @JoinTable (name="segment_membership",
     *      joinColumns={@JoinColumn(name="segment_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn(name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")}
     * )
     */
    protected $memberships;

    /**
     * Use specified products as condition
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $useProducts = false;

    /**
     * Products
     *
     * @var \XLite\Model\Product[]
     *
     * @ManyToMany (targetEntity="\XLite\Model\Product")
     * @JoinTable (name="segment_products",
     *      joinColumns={@JoinColumn(name="segment_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn(name="product_id", referencedColumnName="product_id", onDelete="CASCADE")}
     * )
     */
    protected $products;

    /**
     * Profiles
     *
     * @var \XLite\Model\Profile[]
     *
     * @ManyToMany (targetEntity="XLite\Model\Profile", inversedBy="mail_chimp_segments")
     * @JoinTable  (
     *      name="mailchimp_segment_subscriptions",
     *      joinColumns={@JoinColumn(name="segment_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn(name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")}
     * )
     */
    protected $profiles;

    /**
     * Enabled
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    function __construct(array $data = array())
    {
        $this->memberships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->profiles = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Check if provided profile is subscribes to this list
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    public function isProfileSubscribed(\XLite\Model\Profile $profile)
    {
        return isset($profile)
            ? \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpSegment')
                ->isProfileSubscribed($this, $profile)
            : false;
    }

    /**
     * Check if profile matches segment conditions
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    public function checkProfileConditions(\XLite\Model\Profile $profile)
    {
        $return = true;
        $conditionsEnabled = false;

        if ($this->getUseOrdersLastMonth()) {
            $return = $return && $this->checkOrdersLastMonthCondition($profile);

            $conditionsEnabled = true;
        }

        if ($this->getUseOrderAmount()) {
            $return = $return && $this->checkOrderAmountCondition($profile);

            $conditionsEnabled = true;
        }

        if ($this->getUseMemberships()) {
            $return = $return && $this->checkMembershipsCondition($profile);

            $conditionsEnabled = true;
        }

        if ($this->getUseProducts()) {
            $return = $return && $this->checkProductsCondition($profile);

            $conditionsEnabled = true;
        }

        return $conditionsEnabled && $return;
    }

    /**
     * Check amount of orders for last 30 days
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    public function checkOrdersLastMonthCondition(\XLite\Model\Profile $profile)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->countOrdersLastMonth($profile)
            >= $this->getOrdersLastMonth();
    }

    /**
     * Check total amount for all orders
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    public function checkOrderAmountCondition(\XLite\Model\Profile $profile)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->getOrdersTotal($profile);
    }

    /**
     * Check memberships condition
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    public function checkMembershipsCondition(\XLite\Model\Profile $profile)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->checkProfileMemberships($profile, $this);
    }

    /**
     * Check products condition
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    public function checkProductsCondition(\XLite\Model\Profile $profile)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->checkProductsPurchased($profile, $this);
    }

    /**
     * Subscribe profile to this segment
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return void
     */
    public function doProfileSubscribe(\XLite\Model\Profile $profile)
    {
        $this->addProfiles($profile);

        \XLite\Core\Database::getEM()->persist($this);
        \XLite\Core\Database::getEM()->flush();

        try {
            Core\MailChimp::getInstance()->addToSegment(
                $this->getList()->getId(),
                $this->getId(),
                array($profile->getLogin())
            );
        } catch (Core\MailChimpException $e) {
            \XLite\Core\TopMessage::addError($e->getMessage());
        }
    }

    /**
     * Set id
     *
     * @param string $id
     * @return MailChimpSegment
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MailChimpSegment
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set created_date
     *
     * @param string $createdDate
     * @return MailChimpSegment
     */
    public function setCreatedDate($createdDate)
    {
        $this->created_date = $createdDate;
        return $this;
    }

    /**
     * Get created_date
     *
     * @return string 
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * Set static
     *
     * @param boolean $static
     * @return MailChimpSegment
     */
    public function setStatic($static)
    {
        $this->static = $static;
        return $this;
    }

    /**
     * Get static
     *
     * @return boolean 
     */
    public function getStatic()
    {
        return $this->static;
    }

    /**
     * Set useOrdersLastMonth
     *
     * @param boolean $useOrdersLastMonth
     * @return MailChimpSegment
     */
    public function setUseOrdersLastMonth($useOrdersLastMonth)
    {
        $this->useOrdersLastMonth = $useOrdersLastMonth;
        return $this;
    }

    /**
     * Get useOrdersLastMonth
     *
     * @return boolean 
     */
    public function getUseOrdersLastMonth()
    {
        return $this->useOrdersLastMonth;
    }

    /**
     * Set ordersLastMonth
     *
     * @param integer $ordersLastMonth
     * @return MailChimpSegment
     */
    public function setOrdersLastMonth($ordersLastMonth)
    {
        $this->ordersLastMonth = $ordersLastMonth;
        return $this;
    }

    /**
     * Get ordersLastMonth
     *
     * @return integer 
     */
    public function getOrdersLastMonth()
    {
        return $this->ordersLastMonth;
    }

    /**
     * Set useOrderAmount
     *
     * @param boolean $useOrderAmount
     * @return MailChimpSegment
     */
    public function setUseOrderAmount($useOrderAmount)
    {
        $this->useOrderAmount = $useOrderAmount;
        return $this;
    }

    /**
     * Get useOrderAmount
     *
     * @return boolean 
     */
    public function getUseOrderAmount()
    {
        return $this->useOrderAmount;
    }

    /**
     * Set orderAmount
     *
     * @param decimal $orderAmount
     * @return MailChimpSegment
     */
    public function setOrderAmount($orderAmount)
    {
        $this->orderAmount = $orderAmount;
        return $this;
    }

    /**
     * Get orderAmount
     *
     * @return decimal 
     */
    public function getOrderAmount()
    {
        return $this->orderAmount;
    }

    /**
     * Set useMemberships
     *
     * @param boolean $useMemberships
     * @return MailChimpSegment
     */
    public function setUseMemberships($useMemberships)
    {
        $this->useMemberships = $useMemberships;
        return $this;
    }

    /**
     * Get useMemberships
     *
     * @return boolean 
     */
    public function getUseMemberships()
    {
        return $this->useMemberships;
    }

    /**
     * Set useProducts
     *
     * @param boolean $useProducts
     * @return MailChimpSegment
     */
    public function setUseProducts($useProducts)
    {
        $this->useProducts = $useProducts;
        return $this;
    }

    /**
     * Get useProducts
     *
     * @return boolean 
     */
    public function getUseProducts()
    {
        return $this->useProducts;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return MailChimpSegment
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
     * Set list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list
     * @return MailChimpSegment
     */
    public function setList(\XLite\Module\XC\MailChimp\Model\MailChimpList $list = null)
    {
        $this->list = $list;
        return $this;
    }

    /**
     * Get list
     *
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList 
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Add memberships
     *
     * @param \XLite\Model\Membership $memberships
     * @return MailChimpSegment
     */
    public function addMemberships(\XLite\Model\Membership $memberships)
    {
        $this->memberships[] = $memberships;
        return $this;
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * Add products
     *
     * @param \XLite\Model\Product $products
     * @return MailChimpSegment
     */
    public function addProducts(\XLite\Model\Product $products)
    {
        $this->products[] = $products;
        return $this;
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add profiles
     *
     * @param \XLite\Model\Profile $profiles
     * @return MailChimpSegment
     */
    public function addProfiles(\XLite\Model\Profile $profiles)
    {
        $this->profiles[] = $profiles;
        return $this;
    }

    /**
     * Get profiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProfiles()
    {
        return $this->profiles;
    }
}
