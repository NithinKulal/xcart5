<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Controller\Customer;

/**
 * Abstract customer
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Get fingerprint difference
     *
     * @param array $old Old fingerprint
     * @param array $new New fingerprint
     *
     * @return array
     */
    protected function getCartFingerprintDifference(array $old, array $new)
    {
        $diff = parent::getCartFingerprintDifference($old, $new);

        if (count($old['coupons']) !== count($new['coupons'])
            || count($old['coupons']) !== count(array_intersect($old['coupons'], $new['coupons']))
        ) {
            $diff['coupons'] = array();
            foreach (array_diff($new['coupons'], $old['coupons']) as $id) {
                $diff['coupons'][] = array(
                    'id'    => $id,
                    'state' => 'added',
                );
            }

            foreach (array_diff($old['coupons'], $new['coupons']) as $id) {
                $diff['coupons'][] = array(
                    'id'    => $id,
                    'state' => 'removed',
                );
            }
        }

        return $diff;
    }
}
