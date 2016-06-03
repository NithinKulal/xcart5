<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Logic\Export\Step;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Export\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['wholesalePrices'] = array();

        return $columns;
    }

    /**
     * Get column value for 'wholesalePrices' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getWholesalePricesColumnValue(array $dataset, $name, $i)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\WholesalePrice::P_PRODUCT} = $dataset['model'];

        return $this->convertWholesalePrices(
            \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->search($cnd)
        );
    }

    /**
     * Get Wholesale prices
     *
     * @param array $prices
     *
     * @return array
     */
    protected function convertWholesalePrices(array $prices)
    {
        $result = array();

        foreach ($prices as $price) {
            $str = $price->getQuantityRangeBegin();

            if (0 < $price->getQuantityRangeEnd()) {
                $str .= '-' . $price->getQuantityRangeEnd();
            }

            if ($price->getMembership()) {
                $str .= '(' . $this->formatMembershipModel($price->getMembership()) . ')';
            }

            $str .= '=' . $price->getPrice();

            $result[] = $str;
        }

        return $result;
    }
}
