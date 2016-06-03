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

namespace XLite\Module\CDev\XPaymentsConnector\Model\Payment;

/**
 * X-Payments payment transaction data
 *
 * @Entity
 * @Table  (name="xpc_payment_transaction_data")
 */

class XpcTransactionData extends \XLite\Model\AEntity
{
    /**
     * Allow card usage for recharges 
     */
    const RECHARGE_TRUE  = 'Y';
    const RECHARGE_FALSE = 'N';

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
     * Masked credit card number 
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $card_number = '';

    /**
     * Type of the credit card
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $card_type = '';

    /**
     * Credit card epiration date
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $card_expire = '';

    /**
     * Allow card usage for recharges 
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $use_for_recharges = self::RECHARGE_FALSE;

    /**
     * Billing address 
     *
     * @var \XLite\Model\Address 
     *
     * @ManyToOne  (targetEntity="XLite\Model\Address")
     * @JoinColumn (name="address_id", referencedColumnName="address_id", onDelete="SET NULL")
     */
    protected $billingAddress;

    /**
     * One-to-one relation with payment transaction
     *
     * @var \XLite\Model\Payment\Transaction
     *
     * @OneToOne  (targetEntity="XLite\Model\Payment\Transaction", inversedBy="xpc_data")
     * @JoinColumn (name="transaction_id", referencedColumnName="transaction_id", onDelete="CASCADE")
     */
    protected $transaction;

}
