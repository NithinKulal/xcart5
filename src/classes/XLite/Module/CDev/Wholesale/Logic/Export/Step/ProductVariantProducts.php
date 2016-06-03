<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Logic\Export\Step;

/**
 * Products
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
abstract class ProductVariantProducts extends \XLite\Logic\Export\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns[static::VARIANT_PREFIX . 'WholesalePrices'] = array(static::COLUMN_MULTIPLE => true);

        return $columns;
    }

   /**
     * Get column value for 'variantWholesalePrices' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getVariantWholesalePricesColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        if (
            isset($dataset['variant'])
            && $dataset['variant']
        ) {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\ProductVariantWholesalePrice::P_PRODUCT_VARIANT} = $dataset['variant'];

            $result = $this->convertWholesalePrices(
                \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->search($cnd)
            );

        }

        return $result;
    }
}
