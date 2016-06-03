<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model\OrderItem;

/**
 * Order item's private attachment
 *
 * @Entity
 * @Table  (name="order_item_private_attachments")
 */
class PrivateAttachment extends \XLite\Model\AEntity
{
    // {{{ Columns

    /**
     * Unique id
     *
     * @var   integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Saved title
     *
     * @var   string
     *
     * @Column (type="string", length=255)
     */
    protected $title;

    /**
     * Key
     *
     * @var   string
     *
     * @Column (type="string", options={ "fixed": true }, length=128)
     */
    protected $downloadKey = '';

    /**
     * Expire time (UNIX timestamp)
     *
     * @var   integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $expire = 0;

    /**
     * Attempts count
     *
     * @var   integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $attempt = 0;

    /**
     * Attempts limit
     *
     * @var   integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $attemptLimit = 0;

    /**
     * Blocked status
     *
     * @var   boolean
     *
     * @Column (type="boolean")
     */
    protected $blocked = true;

    // }}}

    // {{{ Associations

    /**
     * Item order
     *
     * @var   \XLite\Model\OrderItem
     *
     * @ManyToOne  (targetEntity="XLite\Model\OrderItem", inversedBy="privateAttachments")
     * @JoinColumn (name="item_id", referencedColumnName="item_id", onDelete="CASCADE")
     */
    protected $item;

    /**
     * Item order
     *
     * @var   \XLite\Model\OrderItem
     *
     * @ManyToOne (targetEntity="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", cascade={"merge", "detach"})
     * @JoinColumn (name="attachment_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $attachment;

    // }}}

    // {{{ Operator

    /**
     * Check atatchment availability
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->getAttachment()
            && $this->getDownloadKey()
            && $this->isOrderCompleted()
            && !$this->getBlocked()
            && !$this->isExpired()
            && !$this->isAttemptsEnded()
            && $this->getAttachment()->getStorage()->isFileExists();
    }

    /**
     * Check attachment activity
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getAttachment()
            && $this->isOrderCompleted();
    }

    /**
     * Check order complete state
     *
     * @return boolean
     */
    public function isOrderCompleted()
    {
        return in_array(
            $this->getItem()->getOrder()->getPaymentStatusCode(),
            array(
                \XLite\Model\Order\Status\Payment::STATUS_PAID,
                \XLite\Model\Order\Status\Payment::STATUS_PART_PAID
            )
        );
    }

    /**
     * Check - has expire limit or not
     *
     * @return boolean
     */
    public function hasExpireLimit()
    {
        return 0 < $this->getExpire();
    }

    /**
     * Check expired status
     *
     * @return boolean
     */
    public function isExpired()
    {
        return $this->hasExpireLimit() && $this->getExpire() < \XLite\Core\Converter::time();
    }

    /**
     * Get expires left (seconds)
     *
     * @return integer
     */
    public function getExpiresLeft()
    {
        return $this->getExpire() - \XLite\Core\Converter::time();
    }

    /**
     * Check - has attempts limit or not
     *
     * @return boolean
     */
    public function hasAttemptsLimit()
    {
        return 0 < $this->getAttemptLimit();
    }

    /**
     * Check attaempts counter state - ended or not
     *
     * @return boolean
     */
    public function isAttemptsEnded()
    {
        return $this->hasAttemptsLimit() && 0 >= $this->getAttemptsLeft();
    }

    /**
     * Get attempts left
     *
     * @return integer
     */
    public function getAttemptsLeft()
    {
        return $this->getAttemptLimit() - $this->getAttempt();
    }

    /**
     * Inrementc attempt
     *
     * @return void
     */
    public function incrementAttempt()
    {
        $this->setAttempt($this->getAttempt() + 1);
    }

    /**
     * Get download URL
     *
     * @return string
     */
    public function getURL()
    {
        return $this->getAttachment()->getStorage()->getDownloadURL($this);
    }

    /**
     * Renew record
     *
     * @return void
     *
     * @PrePersist
     */
    public function renew()
    {
        $this->setDownloadKey($this->generateDownloadKey());
        $ttl = max(0, intval(\XLite\Core\Config::getInstance()->CDev->Egoods->ttl));
        $this->setExpire(0 < $ttl ? \XLite\Core\Converter::time() + $ttl * 86400 : 0);
        $this->setAttempt(0);
        $limit = max(0, intval(\XLite\Core\Config::getInstance()->CDev->Egoods->attempts_limit));
        $this->setAttemptLimit($limit * $this->getItem()->getAmount());
        $this->setBlocked(false);
    }

    /**
     * Get random value for download key
     *
     * @return string
     */
    protected function generateDownloadKey()
    {
        return hash('sha512', \XLite\Core\Database::getRepo('XLite\Model\Profile')->generatePassword());
    }

    // }}}

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
     * Set title
     *
     * @param string $title
     * @return PrivateAttachment
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set downloadKey
     *
     * @param string $downloadKey
     * @return PrivateAttachment
     */
    public function setDownloadKey($downloadKey)
    {
        $this->downloadKey = $downloadKey;
        return $this;
    }

    /**
     * Get downloadKey
     *
     * @return string 
     */
    public function getDownloadKey()
    {
        return $this->downloadKey;
    }

    /**
     * Set expire
     *
     * @param integer $expire
     * @return PrivateAttachment
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
        return $this;
    }

    /**
     * Get expire
     *
     * @return integer 
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Set attempt
     *
     * @param integer $attempt
     * @return PrivateAttachment
     */
    public function setAttempt($attempt)
    {
        $this->attempt = $attempt;
        return $this;
    }

    /**
     * Get attempt
     *
     * @return integer 
     */
    public function getAttempt()
    {
        return $this->attempt;
    }

    /**
     * Set attemptLimit
     *
     * @param integer $attemptLimit
     * @return PrivateAttachment
     */
    public function setAttemptLimit($attemptLimit)
    {
        $this->attemptLimit = $attemptLimit;
        return $this;
    }

    /**
     * Get attemptLimit
     *
     * @return integer 
     */
    public function getAttemptLimit()
    {
        return $this->attemptLimit;
    }

    /**
     * Set blocked
     *
     * @param boolean $blocked
     * @return PrivateAttachment
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;
        return $this;
    }

    /**
     * Get blocked
     *
     * @return boolean 
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * Set item
     *
     * @param \XLite\Model\OrderItem $item
     * @return PrivateAttachment
     */
    public function setItem(\XLite\Model\OrderItem $item = null)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Get item
     *
     * @return \XLite\Model\OrderItem 
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set attachment
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment
     * @return PrivateAttachment
     */
    public function setAttachment(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment = null)
    {
        $this->attachment = $attachment;
        return $this;
    }

    /**
     * Get attachment
     *
     * @return \XLite\Module\CDev\FileAttachments\Model\Product\Attachment 
     */
    public function getAttachment()
    {
        return $this->attachment;
    }
}

