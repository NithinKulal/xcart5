<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\View;

/**
 * Image widget
 */
abstract class Image extends \XLite\View\Image implements \XLite\Base\IDecorator
{
    /**
     * Remove the protocol from the url definition
     *
     * @param string $url
     *
     * @return string
     */
    protected function prepareURL($url)
    {
        $image = $this->getParam(self::PARAM_IMAGE);
        if ($image && $image->isExists() && \XLite\Model\Base\Image::STORAGE_S3 === $image->getStorageType()) {
            return $url;
        }

        return parent::prepareURL($url);
    }
}
