<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Logic\Import\Processor;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['sale'] = array();

        return $columns;
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + array(
                'PRODUCT-SALE-FMT' => 'Wrong sale format',
            );
    }

    /**
     * Verify 'sale' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifySale($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !preg_match('/^\d+\.?\d*(%)?$/', $value)) {
            $this->addWarning('PRODUCT-SALE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    // }}}

    // {{{ Import

    /**
     * Import 'sale' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importSaleColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if ($value) {
            $model->setParticipateSale(true);
            $model->setSalePriceValue(floatval($value));
            $model->setDiscountType(
                strpos($value, '%') > 0
                    ? \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT
                    : \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE
            );

        } else {
            $model->setParticipateSale(false);
        }
    }

    // }}}
}
