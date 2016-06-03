<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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

            if ($cards[0]) {
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

                if ($this->getDefaultCardId() == $res['card_id']) {
                    $res['is_default'] = true;
                }

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

}
