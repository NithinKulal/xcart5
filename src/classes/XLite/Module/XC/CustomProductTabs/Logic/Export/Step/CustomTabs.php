<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Logic\Export\Step;


class CustomTabs extends \XLite\Logic\Export\Step\Base\I18n
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\Tab');
    }

    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'product-custom-tabs.csv';
    }

    // }}}

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'product'       => [],
            'enabled'   => [],
            'position'  => [],
        ];

        $columns += $this->assignI18nColumns([
            'name' => [],
            'content' => [],
        ]);

        return $columns;
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getProductColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getProduct()
            ? $dataset['model']->getProduct()->getSku()
            : '';
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return boolean
     */
    protected function getEnabledColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getEnabled();
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return integer
     */
    protected function getPositionColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getPosition();
    }
}