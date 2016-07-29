<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model;

/**
 * Order item
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Get price
     *
     * @return float
     */
    public function getClearPrice()
    {
        $this->setWholesaleValues();

        return parent::getClearPrice();
    }

    /**
     * Check if item has a wrong amount
     *
     * @return boolean
     */
    public function hasWrongAmount()
    {
        return parent::hasWrongAmount() || $this->hasWrongMinQuantity();
    }

    /**
     * Check if item has an amount less than allowed min quantity
     *
     * @return boolean
     */
    public function hasWrongMinQuantity()
    {
        return $this->getMinQuantity() > $this->getAmount();
    }

    /**
     * Get product minimum quantity
     *
     * @param \XLite\Model\Membership $membership Customer's membership OPTIONAL
     *
     * @return integer
     */
    public function getMinQuantity($membership = null)
    {
        if (is_null($membership) && $this->getOrder()->getProfile()) {
            $membership = $this->getOrder()->getProfile()->getMembership();
        }

        return $this->getProduct()->getMinQuantity($membership);
    }

    /**
     * Check - item price is controlled by server or not
     *
     * @return boolean
     */
    public function isPriceControlledServer()
    {
        $result = parent::isPriceControlledServer();

        if (!$result && $this->getProduct()) {
            $model = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')
                ->findOneBy(array('product' => $this->getProduct()));
            $result = !!$model;
        }

        return $result;
    }

    /**
     * Set wholesale values
     *
     * @return void
     */
    public function setWholesaleValues()
    {
        $this->getProduct()->setWholesaleQuantity($this->getAmount());
        if ($this->getOrder() && $this->getOrder()->getProfile()) {
            $this->getProduct()->setWholesaleMembership(
                $this->getOrder()->getProfile()->getMembership() ?: false
            );
        }
    }
}
