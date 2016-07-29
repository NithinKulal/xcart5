<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Controller\Customer;

/**
 * Cart page controller extension
 */
class Cart extends \XLite\Controller\Customer\Cart implements \XLite\Base\IDecorator
{
    /**
     * Show message about wrong product amount
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return void
     */
    protected function processInvalidAmountError(\XLite\Model\OrderItem $item)
    {
        if ($item->hasWrongMinQuantity()) {
            \XLite\Core\TopMessage::addWarning(
                'The minimum amount of "{{product}}" product {{description}} allowed to purchase is {{min}} item(s). Please adjust the product quantity.',
                array(
                    'product'     => $item->getProduct()->getName(),
                    'description' => $item->getExtendedDescription(),
                    'min'         => $item->getMinQuantity()
                )
            );

        } else {
            parent::processInvalidAmountError($item);
        }
    }
}
