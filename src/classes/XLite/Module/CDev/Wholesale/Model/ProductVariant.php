<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model;

/**
 * Product variant
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XLite\Module\XC\ProductVariants\Model\ProductVariant implements \XLite\Base\IDecorator
{

    /**
     * Get minimum product quantity available to customer to purchase
     *
     * @param \XLite\Model\Membership $membership Customer's membership OPTIONAL
     *
     * @return integer
     */
    public function getMinQuantity($membership = null)
    {
        return $this->getProduct()->getMinQuantity($membership);
    }

    /**
     * Override clear price
     *
     * @return float
     */
    public function getClearPrice()
    {
        $price = parent::getClearPrice();

        if (
            $this->getProduct()->isWholesalePricesEnabled()
            && $this->isPersistent()
         ) {
            $membership = $this->getProduct()->getCurrentMembership();
            $wholesalePrice = $this->getDefaultPrice()
                ? $this->getProduct()->getWholesalePrice($membership)
                : \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->getPrice(
                    $this,
                    $this->getProduct()->getWholesaleQuantity() ?: $this->getProduct()->getMinQuantity($membership),
                    $membership
                );

            if (!is_null($wholesalePrice)) {
                $price = $wholesalePrice;
            }
        }

        return $price;
    }

    /**
     * Return base price
     *
     * @return float
     */
    public function getBasePrice()
    {
        return parent::getClearPrice();
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newEntity = parent::cloneEntity();

        $this->cloneMembership($newEntity);

        return $newEntity;
    }

    /**
     * Clone membership (used in cloneEntity() method)
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $newEntity
     *
     * @return void
     */
    protected function cloneMembership(\XLite\Module\XC\ProductVariants\Model\ProductVariant $newEntity)
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice');

        foreach ($repo->findBy(array('productVariant' => $this)) as $price) {
            $newPrice = $price->cloneEntity();
            $newPrice->setProductVariant($newEntity);
            $newPrice->setMembership($price->getMembership());
            $newPrice->update();
        }
    }
}
