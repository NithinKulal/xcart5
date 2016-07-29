<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model\DTO\Product;

use XLite\Model\DTO\Base\CommonCell;

class Inventory extends \XLite\Model\DTO\Product\Inventory implements \XLite\Base\IDecorator
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        $minimumPurchaseQuantity = [
            'membership_0' => $object->getMinQuantity(),
        ];

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Membership')->findAll() as $membership) {
            $minimumPurchaseQuantity['membership_' . $membership->getMembershipId()] = $object->getMinQuantity($membership);
        }

        $this->minimum_purchase_quantity = new CommonCell($minimumPurchaseQuantity);
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\MinQuantity');
        $membershipRepo = \XLite\Core\Database::getRepo('XLite\Model\Membership');

        $minimumPurchaseQuantity = [];

        foreach ($this->minimum_purchase_quantity as $id => $data) {
            $rate = array(
                'quantity' => max(1, (int) $data),
                'product'  => $object,
            );

            $membership = $membershipRepo->findOneBy(array('membership_id' => str_replace('membership_', '', $id)));
            if ($membership) {
                $rate['membership'] = $membership;
            }

            $minimumPurchaseQuantity[] = $rate;
        }

        $repo->deleteByProduct($object);
        $repo->insertInBatch($minimumPurchaseQuantity);
    }
}
