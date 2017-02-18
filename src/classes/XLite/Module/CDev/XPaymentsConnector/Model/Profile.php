<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model;

/**
 * XPayments payment processor
 *
 */
class Profile extends \XLite\Model\Profile implements \XLite\Base\IDecorator
{

    /**
     * Default card id 
     * 
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $default_card_id = 0;

    /**
     * Pending zero auth (card setup) reference
     *
     * @var string 
     *
     * @Column (type="string")
     */
    protected $pending_zero_auth = '';

    /**
     * Pending zero auth (card setup) txnId 
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $pending_zero_auth_txn_id = '';

    /**
     * Pending zero auth (card setup) status
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $pending_zero_auth_status = '';

    /**
     * Pending zero auth (card setup) interface: cart or admin
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $pending_zero_auth_interface = '';

    /**
     * Get default card id 
     * 
     * @param boolean $strict Strict mode flag OPTIONAL
     *  
     * @return integer
     */
    public function getDefaultCardId($strict = false)
    {
        if (!$this->isCardIdValid($this->default_card_id, $strict)) {

            $cnd = new \XLite\Core\CommonCell();

            $class = '\XLite\Module\CDev\XPaymentsConnector\Model\Repo\Payment\XpcTransactionData';

            $cnd->{$class::SEARCH_RECHARGES_ONLY} = true;
            $cnd->{$class::SEARCH_PAYMENT_ACTIVE} = true;

            if ($strict) {
                $cnd->{$class::SEARCH_PROFILE_ID} = $this->getProfileId();

            } else {
                $cnd->{$class::SEARCH_LOGIN} = $this->getLogin();
            }

            $cards = \XLite\Core\Database::getRepo('XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData')
                ->search($cnd);

            if ($cards && $cards[0]) {
                $this->default_card_id = $cards[0]->getId();
            }

        } 

        return $this->default_card_id;
    }

    /**
     * Get list of saved credit cards 
     *
     * @param boolean $strict Strict mode flag OPTIONAL
     *
     * @return array
     */
    public function getSavedCards($strict = false)
    {
        $result = array();

        if ($this->getLogin()) {

            $cnd = new \XLite\Core\CommonCell();

            $class = '\XLite\Module\CDev\XPaymentsConnector\Model\Repo\Payment\XpcTransactionData';

            $cnd->{$class::SEARCH_RECHARGES_ONLY} = true;
            $cnd->{$class::SEARCH_PAYMENT_ACTIVE} = true;

            if ($strict) {
                $cnd->{$class::SEARCH_PROFILE_ID} = $this->getProfileId();
            } else {
                $cnd->{$class::SEARCH_LOGIN} = $this->getLogin();
            }

            $cards = \XLite\Core\Database::getRepo('XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData')
                ->search($cnd);

            foreach ($cards as $card) {

                $res = array(
                    'card_id'        => $card->getId(),
                    'invoice_id'     => $card->getTransaction()->getOrder()->getOrderNumber(),
                    'order_id'       => $card->getTransaction()->getOrder()->getOrderId(),
                    'profile_id'     => $card->getTransaction()->getOrder()->getProfile()->getProfileId(),
                    'card_number'    => $card->getCardNumber(),
                    'card_type'      => $card->getCardType(),
                    'card_type_css'  => strtolower($card->getCardType()),
                    'expire'         => $card->getCardExpire(),
                    'transaction_id' => $card->getTransaction()->getTransactionId(),
                    'init_action'    => $card->getTransaction()->getInitXpcAction(),
                );

                if ($card->getBillingAddress()) {
                    $res['address'] = \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getAddressItem($card->getBillingAddress());
                    $res['address_id'] = $card->getBillingAddress()->getAddressId();
                }

                $res['is_default'] = ($this->getDefaultCardId() == $res['card_id']);

                $result[] = $res;
            }
        }

        return $result;
    }

    /**
     * Checks if this card belongs to the current profile 
     *
     * @param integer $cardId Card id
     * @param boolean $strict Strict mode flag OPTIONAL
     *
     * @return boolean
     */
    public function isCardIdValid($cardId, $strict = false)
    {
        $cnd = new \XLite\Core\CommonCell();

        $class = '\XLite\Module\CDev\XPaymentsConnector\Model\Repo\Payment\XpcTransactionData';

        $cnd->{$class::SEARCH_RECHARGES_ONLY} = true;
        $cnd->{$class::SEARCH_PAYMENT_ACTIVE} = true;
        $cnd->{$class::SEARCH_CARD_ID} = $cardId;

        if ($strict) {
            $cnd->{$class::SEARCH_PROFILE_ID} = $this->getProfileId();

        } else {
            $cnd->{$class::SEARCH_LOGIN} = $this->getLogin();
        }

        $valid = \XLite\Core\Database::getRepo('XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData')
            ->search($cnd, true);

        return !empty($valid);
    }

    /**
     * Allow recharges for this card
     *
     * @param integer $cardId Card id
     *
     * @return boolean
     */
    public function allowRecharge($cardId)
    {
        return $this->setRecharge($cardId, 'Y');
    }

    /**
     * Deny recharges for this card
     *
     * @param integer $cardId Card id
     *
     * @return boolean
     */
    public function denyRecharge($cardId)
    {
        return $this->setRecharge($cardId, 'N');
    }

    /**
     * Set recharge 
     * 
     * @param integer $cardId   Card id
     * @param string  $recharge Recharge flag
     *  
     * @return boolean
     */
    protected function setRecharge($cardId, $recharge)
    {
        $class = 'XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData';
        $xpcTransaction = \XLite\Core\Database::getRepo($class)->find(intval($cardId));

        $result = false;

        if (
            $xpcTransaction
            && $this->isCardIdValid($cardId)
        ) {
            $xpcTransaction->setUseForRecharges($recharge);
            \XLite\Core\Database::getEM()->flush();

            $result = true;
        }

        return $result;
        
    }


    /**
     * Set default_card_id
     *
     * @param integer $defaultCardId
     * @return Profile
     */
    public function setDefaultCardId($defaultCardId)
    {
        $this->default_card_id = $defaultCardId;
        return $this;
    }

    /**
     * Set pending_zero_auth
     *
     * @param string $pendingZeroAuth
     * @return Profile
     */
    public function setPendingZeroAuth($pendingZeroAuth)
    {
        $this->pending_zero_auth = $pendingZeroAuth;
        return $this;
    }

    /**
     * Get pending_zero_auth
     *
     * @return string 
     */
    public function getPendingZeroAuth()
    {
        return $this->pending_zero_auth;
    }

    /**
     * Set pending_zero_auth_txn_id
     *
     * @param string $pendingZeroAuthTxnId
     * @return Profile
     */
    public function setPendingZeroAuthTxnId($pendingZeroAuthTxnId)
    {
        $this->pending_zero_auth_txn_id = $pendingZeroAuthTxnId;
        return $this;
    }

    /**
     * Get pending_zero_auth_txn_id
     *
     * @return string 
     */
    public function getPendingZeroAuthTxnId()
    {
        return $this->pending_zero_auth_txn_id;
    }

    /**
     * Set pending_zero_auth_status
     *
     * @param string $pendingZeroAuthStatus
     * @return Profile
     */
    public function setPendingZeroAuthStatus($pendingZeroAuthStatus)
    {
        $this->pending_zero_auth_status = $pendingZeroAuthStatus;
        return $this;
    }

    /**
     * Get pending_zero_auth_status
     *
     * @return string 
     */
    public function getPendingZeroAuthStatus()
    {
        return $this->pending_zero_auth_status;
    }

    /**
     * Set pending_zero_auth_interface
     *
     * @param string $pendingZeroAuthInterface
     * @return Profile
     */
    public function setPendingZeroAuthInterface($pendingZeroAuthInterface)
    {
        $this->pending_zero_auth_interface = $pendingZeroAuthInterface;
        return $this;
    }

    /**
     * Get pending_zero_auth_interface
     *
     * @return string 
     */
    public function getPendingZeroAuthInterface()
    {
        return $this->pending_zero_auth_interface;
    }
}
