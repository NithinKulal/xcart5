<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model\Base;

/**
 * Wholesale price model (abstract)
 *
 * @MappedSuperclass
 */
abstract class AWholesalePrice extends \XLite\Model\AEntity
{
    /**
     * Wholesale price unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Value
     *
     * @var float
     *
     * @Column (
     *      type="money",
     *      precision=14,
     *      scale=4,
     *      options={
     *          @\XLite\Core\Doctrine\Annotation\Behavior (list={"taxable"}),
     *          @\XLite\Core\Doctrine\Annotation\Purpose (name="net", source="clear"),
     *          @\XLite\Core\Doctrine\Annotation\Purpose (name="display", source="net")
     *      }
     *  )
     */
    protected $price = 0.0000;

    /**
     * Quantity range (begin)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $quantityRangeBegin = 1;

    /**
     * Quantity range (end)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $quantityRangeEnd = 0;

    /**
     * Relation to a membership entity
     *
     * @var \XLite\Model\Membership
     *
     * @ManyToOne (targetEntity="XLite\Model\Membership")
     * @JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")
     */
    protected $membership;

    /**
     * Return owner
     *
     * @return mixed
     */
    abstract public function getOwner();

    /**
     * Get clear price (required for net and display prices calculation)
     *
     * @return float
     */
    public function getClearPrice()
    {
        return $this->getPrice();
    }

    /**
     * Get net Price
     *
     * @return float
     */
    public function getNetPrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getClearPrice', array('taxable'), 'net');
    }

    /**
     * Get display Price
     *
     * @return float
     */
    public function getDisplayPrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getNetPrice', array('taxable'), 'display');
    }

    /**
     * Get "SAVE" value (percent difference)
     *
     * @return integer
     */
    public function getSavePriceValue()
    {
        if (\XLite::getController() instanceof \XLite\Controller\Customer\ACustomer) {
            $membership = \XLite::getController()->getCart()->getProfile()
                ? \XLite::getController()->getCart()->getProfile()->getMembership()
                : null;

        } else {
            $membership = \XLite\Core\Auth::getInstance()->getProfile()
                ? \XLite\Core\Auth::getInstance()->getProfile()->getMembership()
                : null;
        }

        $price = $this->getRepository()->getPrice(
            $this->getOwner(),
            $this->getOwner()->getMinQuantity($membership),
            $membership
        );

        if (is_null($price)) {
            $price = $this->getOwner()->getBasePrice();
        }

        return max(0, (int)(($price - $this->getPrice()) / $price * 100));
    }

    /**
     * Return true if this price is for 1 item of product and for all customers
     *
     * @return boolean
     */
    public function isDefaultPrice()
    {
        return 1 == $this->getQuantityRangeBegin()
            && is_null($this->getMembership())
            && $this->isPersistent();
    }

    /**
     * Returns "true" if owner is taxable
     *
     * @return boolean
     */
    public function getTaxable()
    {
        return $this->getOwner()->getTaxable();
    }
}
