<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Logic\Export\Step;

/**
 * Products
 */
abstract class Users extends \XLite\Logic\Export\Step\Users implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['socialLoginProvider'] = array();
        $columns['socialLoginId'] = array();
        $columns['pictureUrl'] = array();

        return $columns;
    }

    /**
     * Get column value for 'socialLoginProvider' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getSocialLoginProviderColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'socialLoginProvider');
    }

        /**
     * Get column value for 'socialLoginId' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getSocialLoginIdColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'socialLoginId');
    }

    /**
     * Get column value for 'pictureUrl' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPictureUrlColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'pictureUrl');
    }
}
