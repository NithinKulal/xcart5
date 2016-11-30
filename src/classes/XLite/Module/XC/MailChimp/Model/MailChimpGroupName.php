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
 * @Entity
 * @Table  (name="mailchimp_list_group_name")
 */
class MailChimpGroupName extends \XLite\Model\AEntity
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
     * List name
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $subscriberCount = 0;

    /**
     * MailChimp parent group
     *
     * @var \XLite\Module\XC\MailChimp\Model\MailChimpGroup
     *
     * @ManyToOne (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpGroup", inversedBy="names")
     */
    protected $group;

    /**
     * Profiles
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany (targetEntity="XLite\Model\Profile", inversedBy="mail_chimp_interests")
     * @JoinTable  (
     *      name="mailchimp_profile_interests",
     *      joinColumns={@JoinColumn(name="group_name_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn(name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")}
     * )
     */
    protected $profiles;

    /**
     * Subscribed by default
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $subscribeByDefault = false;

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
        $this->profiles     = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Set id
     *
     * @param string $id
     * @return MailChimpGroupName
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return MailChimpGroupName
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubscribersCount()
    {
        return $this->subscriberCount;
    }

    /**
     * @param int $subscriberCount
     *
     * @return MailChimpGroupName
     */
    public function setSubscribersCount($subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;
        return $this;
    }

    /**
     * @return MailChimpGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param MailChimpGroup $group
     * 
     * @return MailChimpGroupName
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubscriberCount()
    {
        return $this->subscriberCount;
    }

    /**
     * @param int $subscriberCount
     */
    public function setSubscriberCount($subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;
    }

    /**
     * @return bool
     */
    public function getSubscribeByDefault()
    {
        return $this->subscribeByDefault;
    }

    /**
     * @param boolean $subscribeByDefault
     */
    public function setSubscribeByDefault($subscribeByDefault)
    {
        $this->subscribeByDefault = $subscribeByDefault;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Check if provided profile is subscribes to this list
     *
     * @param \XLite\Model\Profile|null $profile Profile
     *
     * @return boolean
     */
    public function isProfileChecked($profile)
    {
        return isset($profile)
            ? $this->getRepository()->isProfileChecked($this, $profile)
            : $this->getSubscribeByDefault();
    }

    /**
     * Add profiles
     *
     * @param \XLite\Model\Profile $profiles
     * @return MailChimpGroupName
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
