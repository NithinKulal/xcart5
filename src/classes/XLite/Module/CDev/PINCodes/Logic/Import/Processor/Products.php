<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Logic\Import\Processor;

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
                'PRODUCT-MANUAL-PIN-FMT' => 'Inventory tracking for product X will not be imported',
            );
    }

    /**
     * Returns true if product has pin codes enabled and pin code autogeneration is turned off
     *
     * @return boolean 
     */
    protected function hasManualPinCodes($sku, $column)
    {
        if (is_null($this->hasManualPinCodes)) {

            $result = false;

            $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBy(
                array(
                    'sku' => $sku
                )
            );

            if ($product && $product->hasManualPinCodes()) {
                $result = true;
                $this->addWarning('PRODUCT-MANUAL-PIN-FMT', array('column' => $column, 'value' => $sku));
            }

            $this->hasManualPinCodes = $result;
        }

        return $this->hasManualPinCodes;
    }

    /**
     * Verify data chunk
     *
     * @param array $data Data chunk
     *
     * @return boolean
     */
    protected function verifyData(array $data)
    {
        $this->hasManualPinCodes = null;

        return parent::verifyData($data);
    }

    /**
     * Import 'inventory tracking enabled' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importInventoryTrackingEnabledColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if (!$this->hasManualPinCodes($model->getSku(), $column)) {
            parent::importInventoryTrackingEnabledColumn($model, $value, $column);
        }
    }

    /**
     * Import 'stock level' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importStockLevelColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if (!$this->hasManualPinCodes($model->getSku(), $column)) {
            parent::importStockLevelColumn($model, $value, $column);
        }
    }
}
