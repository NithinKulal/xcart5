<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;

/**
 * MailChimp mail list
 *
 * @Entity (repositoryClass="\XLite\Module\XC\MailChimp\Model\Repo\MailChimpList")
 * @Table  (name="mailchimp_lists")
 */
class MailChimpList extends \XLite\Model\AEntity
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
    protected $date_created = '';

    /**
     * Last update date
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date_updated = '';

    /**
     * List rating
     *
     * @var float
     *
     * @Column (type="decimal", precision=3, scale=2)
     */
    protected $list_rating = 0.0;

    /**
     * List subscribe URL short
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $subscribe_url_short = '';

    /**
     * List subscribe URL long
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $subscribe_url_long = '';

    /**
     * List member count
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $member_count = 0;

    /**
     * List mail open rate
     *
     * @var float
     *
     * @Column (type="decimal", precision=5, scale=2)
     */
    protected $open_rate = 0.0;

    /**
     * List mail click rate
     *
     * @var float
     *
     * @Column (type="decimal", precision=5, scale=2)
     */
    protected $click_rate = 0.0;

    /**
     * Profiles
     *
     * @var \XLite\Model\Profile[]
     *
     * @ManyToMany (targetEntity="XLite\Model\Profile", inversedBy="mail_chimp_lists")
     * @JoinTable  (
     *      name="mailchimp_subscriptions",
     *      joinColumns={@JoinColumn(name="list_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn(name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")}
     * )
     */
    protected $profiles;

    /**
     * Segments
     *
     * @var \XLite\Module\XC\MailChimp\Model\MailChimpSegment[]
     *
     * @OneToMany (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpSegment", mappedBy="list", cascade={"all"})
     */
    protected $segments;

    /**
     * Groups
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpGroup", mappedBy="list", cascade={"all"})
     */
    protected $groups;

    /**
     * Enabled
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Subscribe by default
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $subscribeByDefault = true;

    /**
     * Defines if the list was removed from MailChimp
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isRemoved = false;

    /**
     * Qty in stock
     *
     * @var \XLite\Module\XC\MailChimp\Model\Store
     *
     * @OneToOne (targetEntity="XLite\Module\XC\MailChimp\Model\Store", mappedBy="list", fetch="LAZY", cascade={"all"})
     */
    protected $store;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    function __construct(array $data = array())
    {
        $this->profiles     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->segments     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups       = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Check if provided profile is subscribes to this list
     *
     * @param \XLite\Model\Profile|null $profile Profile
     *
     * @return boolean
     */
    public function isProfileSubscribed($profile)
    {
        return isset($profile)
            ? \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpList')
                ->isProfileSubscribed($this, $profile)
            : $this->getSubscribeByDefault();
    }

    /**
     * Subscribe profile to list
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return void
     *
     * @throws \Mailchimp_Error
     */
    public function doProfileSubscribe(\XLite\Model\Profile $profile)
    {
        if (!$this->isProfileSubscribed($profile)) {
            $firstName = '';
            $lastName = '';

            if ($profile->getFirstAddress()) {
                $firstName = $profile->getFirstAddress()->getFirstname();
                $lastName = $profile->getFirstAddress()->getLastname();
            }

            try {
                \XLite\Module\XC\MailChimp\Core\MailChimp::getInstance()->doSubscribe(
                    $this->getId(),
                    $profile->getLogin(),
                    $firstName,
                    $lastName
                );
            } catch (\Mailchimp_Error $e) {
                throw $e;
            }

            $this->addProfiles($profile);
            $profile->addMailChimpLists($this);

            \XLite\Core\Database::getEM()->persist($this);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Unsubscribe profile to list
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return void
     *
     * @throws \Mailchimp_Error
     */
    public function doProfileUnsubscribe(\XLite\Model\Profile $profile)
    {
        if ($this->isProfileSubscribed($profile)) {
            try {
                \XLite\Module\XC\MailChimp\Core\MailChimp::getInstance()->doUnsubscribe(
                    $this->getId(),
                    $profile->getLogin()
                );
            } catch (\Mailchimp_Error $e) {
                if ('215' != $e->getCode()) {
                    // Code '215' - email is not subscribed to the list, ignore error.
                    // Otherwise throw an exception
                    throw $e;
                }
            }

            $this->getProfiles()->removeElement($profile);
            $profile->getMailChimpLists()->removeElement($profile);

            \XLite\Core\Database::getEM()->persist($this);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Set id
     *
     * @param string $id
     * @return MailChimpList
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
     * @return MailChimpList
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
     * Set date_created
     *
     * @param string $dateCreated
     * @return MailChimpList
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;
        return $this;
    }

    /**
     * Get date_created
     *
     * @return string 
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set date_updated
     *
     * @param integer $dateUpdated
     * @return MailChimpList
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->date_updated = $dateUpdated;
        return $this;
    }

    /**
     * Get date_updated
     *
     * @return integer 
     */
    public function getDateUpdated()
    {
        return $this->date_updated;
    }

    /**
     * Set list_rating
     *
     * @param decimal $listRating
     * @return MailChimpList
     */
    public function setListRating($listRating)
    {
        $this->list_rating = $listRating;
        return $this;
    }

    /**
     * Get list_rating
     *
     * @return decimal 
     */
    public function getListRating()
    {
        return $this->list_rating;
    }

    /**
     * Set subscribe_url_short
     *
     * @param string $subscribeUrlShort
     * @return MailChimpList
     */
    public function setSubscribeUrlShort($subscribeUrlShort)
    {
        $this->subscribe_url_short = $subscribeUrlShort;
        return $this;
    }

    /**
     * Get subscribe_url_short
     *
     * @return string 
     */
    public function getSubscribeUrlShort()
    {
        return $this->subscribe_url_short;
    }

    /**
     * Set subscribe_url_long
     *
     * @param string $subscribeUrlLong
     * @return MailChimpList
     */
    public function setSubscribeUrlLong($subscribeUrlLong)
    {
        $this->subscribe_url_long = $subscribeUrlLong;
        return $this;
    }

    /**
     * Get subscribe_url_long
     *
     * @return string 
     */
    public function getSubscribeUrlLong()
    {
        return $this->subscribe_url_long;
    }

    /**
     * Set member_count
     *
     * @param integer $memberCount
     * @return MailChimpList
     */
    public function setMemberCount($memberCount)
    {
        $this->member_count = $memberCount;
        return $this;
    }

    /**
     * Get member_count
     *
     * @return integer 
     */
    public function getMemberCount()
    {
        return $this->member_count;
    }

    /**
     * Set open_rate
     *
     * @param decimal $openRate
     * @return MailChimpList
     */
    public function setOpenRate($openRate)
    {
        $this->open_rate = $openRate;
        return $this;
    }

    /**
     * Get open_rate
     *
     * @return decimal 
     */
    public function getOpenRate()
    {
        return $this->open_rate;
    }

    /**
     * Set click_rate
     *
     * @param decimal $clickRate
     * @return MailChimpList
     */
    public function setClickRate($clickRate)
    {
        $this->click_rate = $clickRate;
        return $this;
    }

    /**
     * Get click_rate
     *
     * @return decimal 
     */
    public function getClickRate()
    {
        return $this->click_rate;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return MailChimpList
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
     * Set subscribeByDefault
     *
     * @param boolean $subscribeByDefault
     * @return MailChimpList
     */
    public function setSubscribeByDefault($subscribeByDefault)
    {
        $this->subscribeByDefault = $subscribeByDefault;
        return $this;
    }

    /**
     * Get subscribeByDefault
     *
     * @return boolean 
     */
    public function getSubscribeByDefault()
    {
        return $this->subscribeByDefault;
    }

    /**
     * Set isRemoved
     *
     * @param boolean $isRemoved
     * @return MailChimpList
     */
    public function setIsRemoved($isRemoved)
    {
        $this->isRemoved = $isRemoved;
        return $this;
    }

    /**
     * Get isRemoved
     *
     * @return boolean 
     */
    public function getIsRemoved()
    {
        return $this->isRemoved;
    }

    /**
     * Add profiles
     *
     * @param \XLite\Model\Profile $profiles
     * @return MailChimpList
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

    /**
     * Add segments
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpSegment $segments
     * @return MailChimpList
     */
    public function addSegments(\XLite\Module\XC\MailChimp\Model\MailChimpSegment $segments)
    {
        $this->segments[] = $segments;
        return $this;
    }

    /**
     * Get segments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * Add groups
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpGroup $groups
     * @return MailChimpList
     */
    public function addGroups(\XLite\Module\XC\MailChimp\Model\MailChimpGroup $groups)
    {
        $this->groups[] = $groups;
        return $this;
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }
 
    /**
     * Get enabled groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnabledGroups()
    {
        return $this->getGroups()->filter(function($group){
            return $group->getEnabled();
        });
    }

    /**
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param Store $store
     */
    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }
}
