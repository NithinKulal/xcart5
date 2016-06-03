<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\ItemsList;

/**
 * Product variants items list
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XLite\Module\XC\ProductVariants\View\ItemsList\Model\ProductVariant implements \XLite\Base\IDecorator
{
   /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        foreach ($columns as $k => $v) {
            if ('price' == $k) {
                $columns['wholesalePrices'] =  array(
                    static::COLUMN_CLASS   => 'XLite\Module\CDev\Wholesale\View\FormField\WholesalePrices',
                    static::COLUMN_ORDERBY => $v[static::COLUMN_ORDERBY] + 1,
                );
                break;
            }
        }

        return $columns;
    }
}
