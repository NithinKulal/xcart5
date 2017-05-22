<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\MarketPrice\Logic\Import\Processor;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
        + array(
            'PRODUCT-MARKET-PRICE-FMT' => 'Wrong price format',
        );
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['marketPrice'] = array();

        return $columns;
    }

    /**
     * Normalize 'marketPrice' value
     *
     * @param mixed $value Value
     *
     * @return float
     */
    protected function normalizeMarketPriceValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    /**
     * Verify 'marketPrice' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMarketPrice($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('PRODUCT-MARKET-PRICE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Import 'marketPrice' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importMarketPriceColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if ($value) {
            $model->setMarketPrice(floatval($value));
        }
    }
}
