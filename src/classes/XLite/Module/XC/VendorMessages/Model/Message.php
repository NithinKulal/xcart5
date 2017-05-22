<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model;

/**
 * Message
 *
 * @Entity
 * @Table (name="vendor_convo_messages")
 * @HasLifecycleCallbacks
 */
class Message extends \XLite\Model\AEntity
{

    /**
     * Author types
     */
    const AUTHOR_TYPE_CUSTOMER = 'customer';
    const AUTHOR_TYPE_ADMIN    = 'admin';

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
     * Creation date
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $date;

    /**
     * Body
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $body;

    /**
     * Order
     *
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="messages")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Author
     *
     * @var \XLite\Model\Profile
     *
     * @ManyToOne  (targetEntity="XLite\Model\Profile")
     * @JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $author;

    /**
     * Readers
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\VendorMessages\Model\MessageRead", mappedBy="message", cascade={"all"})
     */
    protected $readers;

    /**
     * @inheritdoc
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);

        $this->readers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Prepare date before create entity
     *
     * @PrePersist
     */
    public function prepareDate()
    {
        $this->setDate(\XLite\Core\Converter::time());
    }

    /**
     * Get public body
     *
     * @return string
     */
    public function getPublicBody()
    {
        $body = $this->getBody();
        $body = preg_replace('/((?:https?|ftp|mailto):\/\/\S+)/Ss', '<a href="$1">$1</a>', $body);
        $body = nl2br($body);

        return $body;
    }

    /**
     * Check - message is readed or not
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return boolean
     */
    public function isRead(\XLite\Model\Profile $profile = null)
    {
        $result = false;
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile() ?: $this->getOrder()->getProfile();
        foreach ($this->getReaders() as $read) {
            if ($read->getReader()->getProfileId() == $profile->getProfileId()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Check - specified profile is message's owner or not
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return boolean
     */
    public function isOwner(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile() ?: $this->getOrder()->getProfile();

        return $profile && $this->getAuthor()->getProfileId() == $profile->getProfileId();
    }

    /**
     * Get author type
     *
     * @return string
     */
    public function getAuthorType()
    {
        return $this->getAuthor()->isAdmin()
            ? static::AUTHOR_TYPE_ADMIN
            : static::AUTHOR_TYPE_CUSTOMER;
    }

    /**
     * Get author name
     *
     * @return string
     */
    public function getAuthorName()
    {
        $data = $this->getInterlocutorData($this->getAuthorType());

        return $data['name'];
    }

    /**
     * Get message target type
     *
     * @return string
     */
    public function getTargetType()
    {
        return $this->getAuthorType() == static::AUTHOR_TYPE_CUSTOMER
            ? static::AUTHOR_TYPE_ADMIN
            : static::AUTHOR_TYPE_CUSTOMER;
    }

    /**
     * Get message target name
     *
     * @return string
     */
    public function getTargetName()
    {
        $data = $this->getInterlocutorData($this->getTargetType());

        return $data['name'];
    }

    /**
     * Get message target email
     *
     * @return string
     */
    public function getTargetEmail()
    {
        $data = $this->getInterlocutorData($this->getTargetType());

        return $data['email'];
    }

    /**
     * Get message target language code
     *
     * @return string
     */
    public function getTargetLanguageCode()
    {
        $data = $this->getInterlocutorData($this->getTargetType());

        return $data['language'];
    }

    /**
     * Get interlocutor data
     *
     * @param string $type Interlocutor type
     *
     * @return string[]|null
     */
    public function getInterlocutorData($type)
    {
        $config = \XLite\Core\Config::getInstance();

        switch ($type) {
            case static::AUTHOR_TYPE_CUSTOMER:
                $profile = $this->getOrder()->getProfile();
                $result = array(
                    'name'     => $profile->getName(),
                    'email'    => $profile->getLogin(),
                    'language' => $profile->getLanguage() ?: $config->General->default_language,
                );
                break;

            case static::AUTHOR_TYPE_ADMIN:
                $result = array(
                    'name'     => $config->Company->company_name,
                    'email'    => $config->Company->site_administrator,
                    'language' => $config->General->default_admin_language,
                );
                break;

            default:
                $result = null;
        }

        return $result;
    }

    /**
     * Mark as read
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return false|\XLite\Module\XC\VendorMessages\Model\MessageRead
     */
    public function markAsRead(\XLite\Model\Profile $profile = null)
    {
        $result = false;
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile() ?: $this->getOrder()->getProfile();

        if (
            $profile
            && $this->getAuthor()
            && !$this->isRead($profile)
        ) {
            $read = new \XLite\Module\XC\VendorMessages\Model\MessageRead;
            $read->setReader($profile);
            $this->addReaders($read);
            $read->setMessage($this);
            $result = $read;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        $this->send();

        return parent::create();
    }

    /**
     * Send message
     *
     * @return static
     */
    public function send()
    {
        \XLite\Core\Mailer::getInstance()->sendVendorMessageNotification($this);

        return $this;
    }

    /**
     * Reset notifications cache
     *
     * @return integer[]
     */
    public function resetNotifications()
    {
        $ids = $this->getNotificationProfileIds();
        if ($ids) {
            $result = \XLite\Core\TmpVars::getInstance()->vendorMessagesUpdateTimestamp;
            if (!is_array($result)) {
                $result = array();
            }

            foreach ($ids as $pid) {
                $result[$pid] = LC_START_TIME;
            }

            \XLite\Core\TmpVars::getInstance()->vendorMessagesUpdateTimestamp = $result;
        }

        return $ids;
    }

    /**
     * Get IDs list for noitifications reseting
     *
     * @return integer[]
     */
    protected function getNotificationProfileIds()
    {
        $list = array();

        if ($this->getTargetType() == static::AUTHOR_TYPE_ADMIN) {
            $list[] = 0;
        }

        return $list;
    }

    // {{{ Setters

    /**
     * Set date
     *
     * @param inegert $date Date
     *
     * @return Message
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return Message
     */
    public function setOrder(\XLite\Model\Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Set author
     *
     * @param \XLite\Model\Profile $author
     *
     * @return Message
     */
    public function setAuthor(\XLite\Model\Profile $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Set readers
     *
     * @param \Doctrine\Common\Collections\Collection $readers
     *
     * @return Message
     */
    public function setReaders(\Doctrine\Common\Collections\Collection $readers)
    {
        $this->readers = $readers;

        return $this;
    }

    /**
     * Add reader
     *
     * @param \XLite\Module\XC\VendorMessages\Model\MessageRead $read Read
     *
     * @return Message
     */
    public function addReaders(\XLite\Module\XC\VendorMessages\Model\MessageRead $read)
    {
        $this->readers->add($read);

        return $this;
    }

    /**
     * Set message's body
     *
     * @param string $body Body
     *
     * @return static
     */
    public function setBody($body)
    {
        $this->body = strip_tags($body);

        return $this;
    }

    // }}}

    // {{{ Getters

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get author
     *
     * @return \XLite\Model\Profile
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Get readers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReaders()
    {
        return $this->readers;
    }

    // }}}

} 