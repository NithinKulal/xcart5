<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\Logic\Export\Step;

/**
 * Products
 */
class Products extends \XLite\Logic\Export\Step\Products implements \XLite\Base\IDecorator
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

        $columns += array(
            'relatedProducts' => array(),
        );

        return $columns;
    }

    /**
     * Get column value for 'relatedProducts' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getRelatedProductsColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        $relProducts = \XLite\Core\Database::getRepo('XLite\Module\XC\Upselling\Model\UpsellingProduct')
            ->getUpsellingProducts($dataset['model']->getProductId());

        foreach ($relProducts as $rel) {
            if ($rel->getProduct()) {
                $result[] = $rel->getProduct()->getSku();
            }
        }

        return $result;
    }

    // }}}
}
