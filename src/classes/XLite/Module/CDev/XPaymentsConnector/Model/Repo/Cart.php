<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model\Repo;

/**
 * Cart repository
 */
class Cart extends \XLite\Model\Repo\Cart implements \XLite\Base\IDecorator
{
    /**
     * Find carts by payment method names 
     *
     * @param array $names List of payment method names
     *
     * @return void
     */
    public function findByPaymentMethodNames($names)
    {
        if (!is_array($names)) {
            $names = array($names);
        }

        $qb = $this->createQueryBuilder()
            ->andWhere('c.payment_method_name IN (:paymentMethodNames)')
            ->setParameter('paymentMethodNames', $names);

        return $qb->getResult();
    }
}
