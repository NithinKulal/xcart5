<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
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
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase\Logic\Order\Modifier;

/**
 * Discount for selected payment method.
 */
class SpecialOffers extends \XLite\Logic\Order\Modifier\Discount
{
    /**
     * Modifier code is the same as a base Discount - this will be aggregated to the single 'Discount' line in cart totals.
     */
    const MODIFIER_CODE = 'SPECIAL_OFFER_DISCOUNT_CHEAPEST';

    /**
     * Modifier type (see \XLite\Model\Base\Surcharge)
     *
     * @var string
     */
    protected $type = \XLite\Model\Base\Surcharge::TYPE_DISCOUNT;

    /**
     * Modifier unique code.
     *
     * @var string
     */
    protected $code = self::MODIFIER_CODE;

    /**
     *
     * @var type Order breakdown into individual units.
     * 
     * @var array
     */
    protected $orderUnits;
    
    
    /**
     * Check - can apply this modifier or not.
     *
     * @return boolean
     */
    public function canApply()
    {
        return parent::canApply();
    }

    /**
     * Calculate.
     *
     * @return void
     */
    public function calculate()
    {
        $this->resetOrderUnits();
        
        foreach ($this->getSpecialOffers() as $offer) {
            $processor = $offer->getOfferType()->getProcessor();
            if ($processor->canApplyOffer($offer, $this)) {
                $processor->applyOffer($offer, $this);
            }
        }
    }

    /**
     * Get surcharge name.
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return \XLite\DataSet\Transport\Order\Surcharge
     */
    public function getSurchargeInfo(\XLite\Model\Base\Surcharge $surcharge)
    {
        $info = new \XLite\DataSet\Transport\Order\Surcharge;
        $info->name = $this->getSurchargeLabel();

        return $info;
    }

    /**
     * Returns the name of the surchage as it will appear on the cart page.
     *
     * @return string
     */
    protected function getSurchargeLabel()
    {
        return \XLite\Core\Translation::lbl('Special Offer discount');
    }


    /**
     * Searches and returns active special offers.
     * 
     * @return array
     */
    protected function getSpecialOffers()
    {
        return $this->getSpecialOfferRepo()->findActiveOffers();
    }
    
    /**
     * Returns the repository object for SpecialOffer model.
     * 
     * @return \XLite\Module\QSL\SpecialOffersBase\Model\Repo\SpecialOffer
     */
    protected function getSpecialOfferRepo()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\QSL\SpecialOffersBase\Model\SpecialOffer');
    }

    /**
     * Add order item surcharge
     *
     * @param \XLite\Model\OrderItem $item Order item
     * @param string $code Surcharge code
     * @param float $value Value
     * @param boolean $include Include flag OPTIONAL
     * @param boolean $available Availability flag OPTIONAL
     *
     * @return \XLite\Model\OrderItem\Surcharge
     */
    public function addItemSurcharge(
        \XLite\Model\OrderItem $item,
        $code,
        $value,
        $include = false,
        $available = true
    )
    {
        return parent::addOrderItemSurcharge($item, $code, $value, $include, $available);
    }

    /**
     * Add order surcharge
     *
     * @param string $code Surcharge code
     * @param float $value Value
     * @param boolean $include Include flag OPTIONAL
     * @param boolean $available Availability flag OPTIONAL
     *
     * @return \XLite\Model\Order\Surcharge
     */
    public function addSurcharge($code, $value, $include = false, $available = true)
    {
        return parent::addOrderSurcharge($code, $value, $include, $available);
    }

    
    /**
     * Returns order items split into individual units.
     * 
     * @return array
     */
    public function getOrderUnits()
    {
        if (!isset($this->orderUnits)) {
            $this->orderUnits = $this->defineOrderUnits();
        }

        return $this->orderUnits;
    }

    /**
     * Breaks order items into individual units.
     * 
     * @return array
     */
    protected function defineOrderUnits()
    {
        $items = array();

        $n = 1;
        foreach ($this->getOrder()->getItems() as $lineItem) {
            $amount = $lineItem->getAmount();
            for ($i = 0; $i < $amount; $i++) {
                $items[] = new \XLite\Module\QSL\SpecialOffersBase\Model\OrderUnit($n++, $lineItem);
            }
        }

        return $items;        
    }

    /**
     * Drops the cached information about order units.
     * 
     * @return void
     */
    protected function resetOrderUnits()
    {
        unset($this->orderUnits);
    }
    
}
