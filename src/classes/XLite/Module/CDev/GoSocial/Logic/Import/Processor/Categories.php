<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Logic\Import\Processor;

/**
 * Import categories processor extension
 */
class Categories extends \XLite\Logic\Import\Processor\Categories implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['useCustomOpenGraphMeta'] = array();
        $columns['openGraphMeta'] = array(
            static::COLUMN_IS_TAGS_ALLOWED => true,
            static::COLUMN_IS_TRUSTED      => true,
        );

        return $columns;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + array(
                'USER-USE-OG-META-FMT' => 'Wrong format of UseCustomOpenGraphMeta value',
            );
    }

    /**
     * Verify 'useCustomOpenGraphMeta' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyUseCustomOpenGraphMeta($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('USER-USE-OG-META-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Import 'useCustomOpenGraphMeta' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param mixed                 $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importUseCustomOpenGraphMetaColumn(\XLite\Model\Category $model, $value, array $column)
    {
        $model->setUseCustomOG($this->normalizeValueAsBoolean($value));
    }

    /**
     * Import 'openGraphMeta' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param mixed                 $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importOpenGraphMetaColumn(\XLite\Model\Category $model, $value, array $column)
    {
        if (!$model->getUseCustomOG()) {
            $value = $model->getOpenGraphMetaTags(false);

        } elseif (is_array($value)) {
            $value = implode(PHP_EOL, $value);
        }

        $model->setOgMeta($value);
    }
}
