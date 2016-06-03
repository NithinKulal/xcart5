<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\FormModel\Product;

class Inventory extends \XLite\View\FormModel\Product\Inventory implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineSections()
    {
        $sections = parent::defineSections();
        $sections['minimum_purchase_quantity'] = static::t('Minimum purchase quantity');

        return $sections;
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $result = parent::defineFields();

        $position = 100;
        $minimumPurchaseQuantity = [
            'membership_0' => [
                'label' => static::t('All customers'),
                'type'    => 'XLite\View\FormModel\Type\PatternType',
                'pattern' => [
                    'alias'      => 'integer',
                    'rightAlign' => false,
                ],
                'position' => $position,
            ],
        ];

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Membership')->findAll() as $membership) {
            $position += 100;
            $minimumPurchaseQuantity['membership_' . $membership->getMembershipId()] = [
                'label' => $membership->getName(),
                'type'    => 'XLite\View\FormModel\Type\PatternType',
                'pattern' => [
                    'alias'      => 'integer',
                    'rightAlign' => false,
                ],
                'position' => $position,
            ];
        }

        $result['minimum_purchase_quantity'] = $minimumPurchaseQuantity;

        return $result;
    }
}
