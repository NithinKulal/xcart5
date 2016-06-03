<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Logic\Import\Processor;

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

        $columns['wholesalePrices'] = array(
            static::COLUMN_IS_MULTIPLE => true,
        );

        return $columns;
    }

    // }}}

    // {{{ Verification

    /**
     * Verify 'wholesalePrices' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyWholesalePrices($value, array $column)
    {
    }

    // }}}

    // {{{ Import

    /**
     * Import 'wholesalePrices' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importWholesalePricesColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        foreach (\XLite\Core\Database::getRepo('\XLite\Module\CDev\Wholesale\Model\WholesalePrice')->findByProduct($model) as $price) {
            \XLite\Core\Database::getRepo('\XLite\Module\CDev\Wholesale\Model\WholesalePrice')->delete($price);
        }
        if ($value) {
            foreach ($value as $price) {
                if (preg_match('/^(\d+)(-(\d+))?(\((.+)\))?=(\d+\.?\d*)$/iSs', $price, $m)) {
                    \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->insert(
                        array(
                            'membership'         => $this->normalizeValueAsMembership($m[5]),
                            'product'            => $model,
                            'price'              => $m[6],
                            'quantityRangeBegin' => $m[1],
                            'quantityRangeEnd'   => intval($m[3]),
                        ),
                        false
                    );
                }
            }
        }
    }

    // }}}
}
