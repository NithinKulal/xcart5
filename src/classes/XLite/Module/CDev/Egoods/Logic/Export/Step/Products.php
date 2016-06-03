<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Logic\Export\Step;

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

        $columns['attachmentsPrivate'] = array();

        return $columns;
    }

    /**
     * Get column value for 'attachmentsPrivate' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getAttachmentsPrivateColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        foreach ($dataset['model']->getAttachments() as $attachment) {
            $result[] = $this->formatBoolean($attachment->getPrivate());
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
        return $this->formatStorageModel(
            $attachment->getStorage(),
            $attachment->getPrivate() ?: null
        );
    }
}
