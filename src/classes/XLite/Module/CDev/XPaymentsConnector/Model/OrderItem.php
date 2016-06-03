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
 * Fake order item for zero auth's and recharges from X-Paymemts.
 * Something customer can not put into his cart
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Flag for zero auth and recharges
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $xpcFakeItem = false;

    /**
     * Is this item a fake one for zero auth and recharges
     *
     * @return boolean
     */
    public function isXpcFakeItem()
    {
        return $this->getXpcFakeItem();
    }

    /**
     * Check if item is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->isXpcFakeItem()
            || parent::isValid();
    }

    /**
     * Deleted Item flag
     *
     * @return boolean
     */
    public function isDeleted()
    {
        $result = parent::isDeleted();

        if ($this->isXpcFakeItem()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Returns deleted product for fake items
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        if ($this->isXpcFakeItem()) {
            return $this->getDeletedProduct();
        } else {
            return parent::getProduct();
        }
    }

    /**
     * Check if the item is valid to clone through the Re-order functionality
     *
     * @return boolean
     */
    public function isValidToClone()
    {
        if ($this->isXpcFakeItem()) {

            $result = false;

        } else {

            $result = parent::isValidToClone();
        }

        return $result;
    }
}
