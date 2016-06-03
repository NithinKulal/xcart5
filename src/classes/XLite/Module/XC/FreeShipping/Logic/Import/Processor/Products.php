<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Logic\Import\Processor;

/**
 * Decorate import processor
 */
class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
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

        $columns['freeShipping'] = array();
        $columns['freightFixedFee'] = array();

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
                'PRODUCT-FREE-SHIPPING-FMT' => 'Wrong free shipping format',
                'PRODUCT-FREIGHT-FIXED-FEE-FMT' => 'Wrong freight fixed fee format',
            );
    }

    /**
     * Verify 'free shipping' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyFreeShipping($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('PRODUCT-FREE-SHIPPING-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'freightFixedFee' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyFreightFixedFee($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('PRODUCT-FREIGHT-FIXED-FEE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Normalize 'freightFixedFee' value
     *
     * @param mixed $value Value
     *
     * @return float
     */
    protected function normalizeFreightFixedFeeValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    // }}}

    // {{{ Import

    /**
     * Import 'free shipping' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importFreeShippingColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $model->setFreeShip($this->normalizeValueAsBoolean($value));
    }

    // }}}
}
