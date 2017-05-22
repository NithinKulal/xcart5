<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Logic\Import\Processor;

/**
 * Abstract import processor
 */
abstract class AProcessor extends \XLite\Logic\Import\Processor\AProcessor implements \XLite\Base\IDecorator
{
    /**
     * Process current row
     *
     * @param string $mode Mode
     *
     * @return boolean
     */
    public function processCurrentRow($mode)
    {
        \XLite\Model\Base\Image::setImportRunning(true);

        return parent::processCurrentRow($mode);
    }

    /**
     * Verify value as URL
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsURL($value)
    {
        $result = parent::verifyValueAsURL($value);

        if (
            !$result
            && \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->isValid()
            && \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->isMatchS3Url($value)
        ) {
            return true;
        }

        return $result;
    }
}
