<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Logic\Export\Step;

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

        $columns['attachments'] = array();
        $columns += $this->assignI18nColumns(
            array(
                'attachmentsTitle'       => array(static::COLUMN_GETTER => 'getAttachmentsTitleColumnValue'),
                'attachmentsDescription' => array(static::COLUMN_GETTER => 'getAttachmentsDescriptionColumnValue'),
            )
        );

        return $columns;
    }

    /**
     * Get column value for 'attachments' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getAttachmentsColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        foreach ($dataset['model']->getAttachments() as $attachment) {
            $result[] = $this->formatAttachmentModel($attachment);
        }

        return $result;
    }

    /**
     * Get column value for 'attachmentsTitle' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getAttachmentsTitleColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        foreach ($dataset['model']->getAttachments() as $attachment) {
            $result[] = $attachment->getTranslation(substr($name, -2))->getTitle();
        }

        return $result;
    }

    /**
     * Get column value for 'attachmentsDescription' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getAttachmentsDescriptionColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        foreach ($dataset['model']->getAttachments() as $attachment) {
            $result[] = $attachment->getTranslation(substr($name, -2))->getDescription();
        }

        return $result;
    }

    /**
     * Format attachment model
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment Attachment
     *
     * @return string
     */
    protected function formatAttachmentModel(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment)
    {
        return $this->formatStorageModel($attachment->getStorage());
    }
}
