<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Logic\Import\Processor;

/**
 * Customers
 */
abstract class Customers extends \XLite\Logic\Import\Processor\Customers implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['socialLoginProvider'] = array(
            static::COLUMN_LENGTH => 255
        );
        $columns['socialLoginId'] = array(
            static::COLUMN_LENGTH => 255
        );
        $columns['pictureUrl'] = array();

        return $columns;
    }

    /**
     * Import 'socialLoginProvider' value
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importSocialLoginProviderColumn(\XLite\Model\Profile $model, $value, array $column)
    {
        if ($value) {
            $model->setSocialLoginProvider($value);
        }
    }

    /**
     * Import 'socialLoginId' value
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importSocialLoginIdColumn(\XLite\Model\Profile $model, $value, array $column)
    {
        if ($value) {
            $model->setSocialLoginId($value);
        }
    }

    /**
     * Import 'pictureUrl' value
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importPictureUrlColumn(\XLite\Model\Profile $model, $value, array $column)
    {
        if ($value && $this->verifyValueAsURL($value)) {
            $model->setPictureUrl($value);
        }
    }
}
