<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export\Step;

/**
 * Attributes
 */
class Attributes extends \XLite\Logic\Export\Step\Base\I18n
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Attribute');
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
        $columns = array(
            'position'     => array(),
            'type'         => array(),
            'product'      => array(),
        );

        $columns += $this->assignI18nColumns(
            array(
                'name'    => array(),
                'class'   => array(static::COLUMN_GETTER => 'getClassColumnValue'),
                'group'   => array(static::COLUMN_GETTER => 'getGroupColumnValue'),
                'options' => array(static::COLUMN_GETTER => 'getOptionsColumnValue'),
            )
        );

        return $columns;
    }

    // }}}

    // {{{ Getters and formatters

    /**
     * Get column value for 'position' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPositionColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'position');
    }

    /**
     * Get column value for 'type' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getTypeColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'type');
    }

    /**
     * Get column value for 'class' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getClassColumnValue(array $dataset, $name, $i)
    {
        $class = $dataset['model']->getProductClass();

        return $class
            ? $class->getTranslation(substr($name, -2))->getName()
            : '';
    }

    /**
     * Get column value for 'group' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getGroupColumnValue(array $dataset, $name, $i)
    {
        $group = $dataset['model']->getAttributeGroup();

        return $group
            ? $group->getTranslation(substr($name, -2))->getName()
            : '';
    }

    /**
     * Get column value for 'options' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getOptionsColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        foreach ($dataset['model']->getAttributeOptions() as $option) {
            $result[] = $option->getTranslation(substr($name, -2))->getName();
        }

        return $result;
    }

    /**
     * Get column value for 'product' column
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

    // }}}
}
