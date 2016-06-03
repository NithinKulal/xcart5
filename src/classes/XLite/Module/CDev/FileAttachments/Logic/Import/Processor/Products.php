<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Logic\Import\Processor;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
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

        $columns['attachments'] = array(
            static::COLUMN_IS_MULTIPLE => true,
        );
        $columns['attachmentsTitle'] = array(
            static::COLUMN_IS_MULTIPLE     => true,
            static::COLUMN_IS_MULTILINGUAL => true,
            static::COLUMN_LENGTH          => 128,
        );
        $columns['attachmentsDescription'] = array(
            static::COLUMN_IS_MULTIPLE     => true,
            static::COLUMN_IS_MULTILINGUAL => true,
        );

        return $columns;
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + array(
                'PRODUCT-ATTACH-FMT' => 'The "{{value}}" file is not created',
            );
    }

    /**
     * Verify 'attachments' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyAttachments($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $attachment) {
                if (!$this->verifyValueAsEmpty($attachment) && !$this->verifyValueAsFile($attachment)) {
                    $this->addWarning('PRODUCT-ATTACH-FMT', array('column' => $column, 'value' => $attachment));
                }
            }
        }
    }

    // }}}

    // {{{ Import

    /**
     * Import 'attachments' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importAttachmentsColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        if ($value) {
            foreach ($value as $index => $path) {
                $path = $this->verifyValueAsLocalURL($path) ? $this->getLocalPathFromURL($path) : $path;
                if ($this->verifyValueAsFile($path)) {
                    $attachment = $model->getAttachments()->get($index);
                    if (!$attachment) {
                        $attachment = new \XLite\Module\CDev\FileAttachments\Model\Product\Attachment();
                        $attachment->setProduct($model);
                        $model->getAttachments()->add($attachment);

                        \XLite\Core\Database::getEM()->persist($attachment);
                    }

                    if (1 < count(parse_url($path))) {
                        $attachment->getStorage()->loadFromURL($path, true);

                    } else {
                        $attachment->getStorage()->loadFromLocalFile(LC_DIR_ROOT . $path);
                    }
                }
            }

            while (count($model->getAttachments()) > count($value)) {
                $attachment = $model->getAttachments()->last();
                \XLite\Core\Database::getRepo('XLite\Module\CDev\FileAttachments\Model\Product\Attachment')->delete($attachment, false);
                $model->getAttachments()->removeElement($attachment);
            }
        }
    }

    /**
     * Import 'attachmentsTitle' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importAttachmentsTitleColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        if ($value) {
            foreach ($value as $index => $val) {
                $attachment = $model->getAttachments()->get($index);
                if ($attachment) {
                    $this->updateModelTranslations($attachment, $val, 'title');
                }
            }
        }
    }

    /**
     * Import 'attachmentsDescription' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importAttachmentsDescriptionColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        if ($value) {
            foreach ($value as $index => $val) {
                $attachment = $model->getAttachments()->get($index);
                if ($attachment) {
                    $this->updateModelTranslations($attachment, $val, 'description');
                }
            }
        }
    }

    // }}}
}
