<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View;

/**
 * Volume discounts promotion block widget in the cart
 *
 * @ListChild (list="cart.panel.totals", weight="100")
 */
class CartPromo extends \XLite\View\AView
{
    /**
     * nextDiscount
     *
     * @var \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    protected $nextDiscount;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/VolumeDiscounts/cart.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/VolumeDiscounts/cart_promo.twig';
    }

    /**
     * Get current discount rate applied to cart
     *
     * @return \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    protected function getCurrentDiscount()
    {
        /** @var \XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount');

        return $repo->getFirstDiscount($this->getCurrentDiscountCondition());
    }

    /**
     * Returns current discount condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getCurrentDiscountCondition()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_SUBTOTAL}
            = $this->getCart()->getSubtotal();

        $profile = $this->getCart()->getProfile();
        $membership = $profile ? $profile->getMembership() : null;
        if ($membership) {
            $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_MEMBERSHIP}
                = $membership;
        }

        return $cnd;
    }

    /**
     * Get next discount rate available for cart subtotal
     *
     * @return \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    protected function getNextDiscount()
    {
        if (null === $this->nextDiscount) {
            /** @var \XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount');
            $this->nextDiscount = $repo->getNextDiscount($this->getNextDiscountCondition());
        }

        return $this->nextDiscount;
    }

    /**
     * Returns next discount condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getNextDiscountCondition()
    {
        $cnd = new \XLite\Core\CommonCell();

        /** @var \XLite\Model\Cart $cart */
        $cart = $this->getCart();
        $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_SUBTOTAL_ADV}
            = $cart->getSubtotal();

        /** @var \XLite\Model\Profile $profile */
        $profile = $cart->getProfile();
        $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_MEMBERSHIP}
            = $profile ? $profile->getMembership() : null;
        /** @var \XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount $repo */

        return $cnd;
    }

    /**
     * Returns true if next discount rate is available for cart
     *
     * @return boolean
     */
    protected function hasNextDiscount()
    {
        if (null === $this->nextDiscount) {
            $this->nextDiscount = $this->getNextDiscount();

            if (null !== $this->nextDiscount) {
                $nextValue = $this->getCart()->getCurrency()->formatValue(
                    $this->nextDiscount->getAmount($this->getCart())
                );

                $currentValue = 0;

                if (0 < $nextValue) {
                    $currentDiscount = $this->getCurrentDiscount();

                    if ($currentDiscount) {
                        $currentValue = $this->getCart()->getCurrency()->formatValue(
                            $currentDiscount->getAmount($this->getCart())
                        );
                    }
                }

                if ($nextValue <= $currentValue) {
                    $this->nextDiscount = null;
                }
            }
        }

        return null !== $this->nextDiscount;
    }

    /**
     * Get formatted next discount subtotal
     *
     * @return string
     */
    protected function getNextDiscountSubtotal()
    {
        $result = '';

        $discount = $this->getNextDiscount();
        if (null !== $discount) {
            $result = static::formatPrice($discount->getSubtotalRangeBegin(), $this->getCart()->getCurrency(), true);
        }

        return $result;
    }

    /**
     * Get formatted next discount value
     *
     * @return string
     */
    protected function getNextDiscountValue()
    {
        $result = '';

        $discount = $this->getNextDiscount();
        if (null !== $discount) {
            if ($discount->isAbsolute()) {
                $result = static::formatPrice($discount->getValue(), $this->getCart()->getCurrency(), true);

            } else {
                $str = sprintf('%0.f', $discount->getValue());
                $precision = strlen(sprintf('%d', (int) substr($str, strpos($str, '.') + 1)));
                $result = round($discount->getValue(), $precision) . '%';
            }
        }

        return $result;
    }
}
