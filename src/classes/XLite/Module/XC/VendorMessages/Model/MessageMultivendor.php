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
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class MessageMultivendor extends \XLite\Module\XC\VendorMessages\Model\Message implements \XLite\Base\IDecorator
{

    /**
     * Author types
     */
    const AUTHOR_TYPE_VENDOR = 'vendor';

    /**
     * Dispute states
     */
    const DISPUTE_STATE_NONE  = 0;
    const DISPUTE_STATE_OPEN  = 1;
    const DISPUTE_STATE_CLOSE = 2;

    /**
     * Dispute state
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $dispute_state = self::DISPUTE_STATE_NONE;

    /**
     * Get dispute state
     *
     * @return integer
     */
    public function getDisputeState()
    {
        return $this->dispute_state;
    }

    /**
     * Set dispute state
     *
     * @param integer $dispute_state State
     *
     * @return static
     */
    public function setDisputeState($dispute_state)
    {
        $this->dispute_state = $dispute_state;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAuthorType()
    {
        return $this->getAuthor()->isVendor()
            ? static::AUTHOR_TYPE_VENDOR
            : parent::getAuthorType();
    }

    /**
     * @inheritdoc
     */
    public function getTargetType()
    {
        return (\XLite\Module\XC\VendorMessages\Main::isVendorAllowed() && $this->getOrder()->getVendor())
            ? ($this->getAuthorType() == static::AUTHOR_TYPE_CUSTOMER ? static::AUTHOR_TYPE_VENDOR : static::AUTHOR_TYPE_CUSTOMER)
            : parent::getTargetType();
    }

    /**
     * Open dispute
     *
     * @return boolean
     */
    public function openDispute()
    {
        $result = false;

        if (!$this->getOrder()->getIsOpenedDispute()) {
            if (!$this->getBody()) {
                $this->setBody(static::t('Dispute opened by X', array('name' => $this->getAuthorName())));
            }
            $this->setDisputeState(static::DISPUTE_STATE_OPEN);
            $this->getOrder()->setIsOpenedDispute(true);
            if ($this->getAuthorType() == static::AUTHOR_TYPE_CUSTOMER) {
                \XLite\Core\TmpVars::getInstance()->vendorDisputesUpdateTimestamp = LC_START_TIME;
            }
            $result = true;
        }

        return $result;
    }

    /**
     * Close dispute
     *
     * @return boolean
     */
    public function closeDispute()
    {
        $result = false;

        if ($this->getOrder()->getIsOpenedDispute()) {
            if (!$this->getBody()) {
                $this->setBody(static::t('Dispute closed by X', array('name' => $this->getAuthorName())));
            }
            $this->setDisputeState(static::DISPUTE_STATE_CLOSE);
            $this->getOrder()->setIsOpenedDispute(false);
            $result = true;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getInterlocutorData($type)
    {
        $data = parent::getInterlocutorData($type);
        if (!$data && $type == static::AUTHOR_TYPE_VENDOR) {
            $profile = $this->getOrder()->getVendor();
            $data = array(
                'name'     => $profile->getVendorNameForMessages(),
                'email'    => $profile->getLogin(),
                'language' => $profile->getLanguage() ?: \XLite\Core\Config::getInstance()->General->default_admin_language,
            );
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function send()
    {
        parent::send();

        if (\XLite\Module\XC\VendorMessages\Main::isVendorAllowed() && $this->getOrder()->getVendor()) {
            if ($this->getAuthorType() == static::AUTHOR_TYPE_ADMIN) {

                // From admin to vendor
                \XLite\Core\Mailer::getInstance()->sendVendorMessageNotification(
                    $this,
                    static::AUTHOR_TYPE_VENDOR
                );

            } elseif (
                $this->getOrder()->getIsOpenedDispute()
                || $this->getOrder()->getIsWatchMessages()
                || $this->getDisputeState() == static::DISPUTE_STATE_CLOSE
            ) {

                // From non-admin to To admin
                \XLite\Core\Mailer::getInstance()->sendVendorMessageNotification(
                    $this,
                    static::AUTHOR_TYPE_ADMIN
                );
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getNotificationProfileIds()
    {
        $list = parent::getNotificationProfileIds();

        if (
            \XLite\Module\XC\VendorMessages\Main::isVendorAllowed()
            && $this->getOrder()->getVendor()
        ) {
            if (
                !$list
                && \XLite\Module\XC\VendorMessages\Main::isWarehouse()
                && $this->getOrder()->getIsOpenedDispute()
            ) {
                $list[] = 0;
            }

            $list[] = $this->getOrder()->getVendor()->getProfileId();
        }

        return $list;
    }

} 