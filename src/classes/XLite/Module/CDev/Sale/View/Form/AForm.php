<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Form;

/**
 * "Set the sale price" dialog form class
 */
class AForm extends \XLite\View\Form\AForm implements \XLite\Base\IDecorator
{
    /**
     * Set validators pairs for products data. Sale structure.
     *
     * @param mixed &$data Data
     *
     * @return void
     */
    protected function setSaleDataValidators(&$data)
    {
        if ($this->getPostedData('participateSale')) {
            switch ($this->getPostedData('discountType')) {

                case \XLite\Module\CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PRICE:
                    $data->addPair('salePriceValue', new \XLite\Core\Validator\TypeFloat(), null, 'Sale price')
                        ->setRange(0);
                    break;

                case \XLite\Module\CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PERCENT:
                    $data->addPair('salePriceValue', new \XLite\Core\Validator\TypeInteger(), null, 'Percent off')
                        ->setRange(1, 100);
                    break;

                default:
            }
        }
    }
}
