<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;
use XLite\Module\XC\MailChimp\Core\MailChimp;

/**
 * The "profile" model class
 */
abstract class Profile extends \XLite\Model\Profile implements \XLite\Base\IDecorator
{
    /**
     * MailChimp lists
     *
     * @var \XLite\Module\XC\MailChimp\Model\MailChimpList[]
     *
     * @ManyToMany (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpList", mappedBy="profiles")
     */
    protected $mail_chimp_lists;

    /**
     * MailChimp lists
     *
     * @var \XLite\Module\XC\MailChimp\Model\MailChimpGroupName[]
     *
     * @ManyToMany (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpGroupName", mappedBy="profiles")
     */
    protected $mail_chimp_interests;

    /**
     * MailChimp segments
     *
     * @var \XLite\Module\XC\MailChimp\Model\MailChimpSegment[]
     *
     * @ManyToMany (targetEntity="XLite\Module\XC\MailChimp\Model\MailChimpSegment", mappedBy="profiles")
     */
    protected $mail_chimp_segments;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    function __construct(array $data = array())
    {
        $this->mail_chimp_lists = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mail_chimp_segments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mail_chimp_interests = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Check if profile is subscribed to provided MailChimp list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list MailChimp list
     *
     * @return boolean
     */
    public function isSubscribedToMailChimpList(\XLite\Module\XC\MailChimp\Model\MailChimpList $list)
    {
        return $list->isProfileSubscribed($this);
    }

    /**
     * Check if profile is subscribed to provided MailChimp segment
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment Segment
     *
     * @return boolean
     */
    public function isSubscribedToMailChimpSegment(\XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment)
    {
        return $segment->isProfileSubscribed($this);
    }

    /**
     * Check if profile has any MailChimp subscriptions
     *
     * @return boolean
     */
    public function hasMailChimpSubscriptions()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->hasMailChimpSubscriptions($this);
    }

    /**
     * Get subscribed MailChimp lists IDs
     *
     * @return array
     */
    public function getMailChimpListsIds()
    {
        $return = array();

        foreach ($this->getMailChimpLists() as $list) {
            $return[] = $list->getId();
        }

        return $return;
    }

    /**
     * Check if there any segments profile can be subscribed to
     *
     * @return void
     */
    public function checkSegmentsConditions()
    {
        foreach ($this->getMailChimpLists() as $list) {
            foreach ($list->getSegments() as $segment) {
                if (
                    $segment->getEnabled()
                    && $segment->getStatic()
                    && !$segment->isProfileSubscribed($this)
                    && $segment->checkProfileConditions($this)
                ) {
                    $segment->doProfileSubscribe($this);
                }
            }
        }
    }

    /**
     * Check if there any groups profile can be subscribed to
     *
     * @param       $listId
     * @param array $groupNameIds
     *
     * @internal param array $groupNames
     */
    public function checkGroupsConditions($listId, array $groupNameIds)
    {
        $groupNameIds = array_map(function($value) { return !!$value; }, $groupNameIds);

        MailChimp::getInstance()->addInterestsToMember(
            $listId,
            $this->getLogin(),
            $groupNameIds
        );

        foreach ($groupNameIds as $nameId => $value) {
            /** @var MailChimpGroupName $name */
            $name = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpGroupName')
                ->find($nameId);

            if ($value) {
                if (!$this->getMailChimpInterests()->contains($name)) {
                    $name->addProfiles($this);
                    $this->addMailChimpInterests($name);
                }

            } else {
                $name->getProfiles()->removeElement($this);
                $this->getMailChimpInterests()->removeElement($name);
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Subscribe profile to MailChimp lists specified by IDs
     *
     * @param array $listsIds MailChimp lists IDs
     *
     * @return void
     */
    public function doSubscribeToMailChimpLists($listsIds)
    {
        foreach ($listsIds as $listId) {
            $list = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpList')->find($listId);

            if (isset($list)) {
                $list->doProfileSubscribe($this);
            }
        }
    }

    /**
     * Unsubscribe profile from MailChimp lists specified by IDs
     *
     * @param array $listsIds MailChimp lists IDs
     *
     * @return void
     */
    public function doUnsubscribeFromMailChimpLists(array $listsIds)
    {
        $lists = $this->getMailChimpLists();

        foreach ($lists as $i => $l) {
            if (in_array($l->getId(), $listsIds)) {
                $l->doProfileUnsubscribe($this);
            }
        }
    }

    /**
     * Add mail_chimp_lists
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $mailChimpLists
     * @return Profile
     */
    public function addMailChimpLists(\XLite\Module\XC\MailChimp\Model\MailChimpList $mailChimpLists)
    {
        $this->mail_chimp_lists[] = $mailChimpLists;
        return $this;
    }

    /**
     * Get mail_chimp_lists
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMailChimpLists()
    {
        return $this->mail_chimp_lists;
    }

    /**
     * Add mail_chimp_segments
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpSegment $mailChimpSegments
     * @return Profile
     */
    public function addMailChimpSegments(\XLite\Module\XC\MailChimp\Model\MailChimpSegment $mailChimpSegments)
    {
        $this->mail_chimp_segments[] = $mailChimpSegments;
        return $this;
    }

    /**
     * Get mail_chimp_segments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMailChimpSegments()
    {
        return $this->mail_chimp_segments;
    }
    
    /**
     * Add mail_chimp_segments
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpGroupName $mailChimpGroupName
     * @return Profile
     */
    public function addMailChimpInterests(\XLite\Module\XC\MailChimp\Model\MailChimpGroupName $mailChimpGroupName)
    {
        $this->mail_chimp_interests[] = $mailChimpGroupName;
        return $this;
    }

    /**
     * Get mail_chimp_segments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMailChimpInterests()
    {
        return $this->mail_chimp_interests;
    }
}
