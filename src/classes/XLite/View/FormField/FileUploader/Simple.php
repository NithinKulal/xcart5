<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\FileUploader;

/**
 * Simple file uploader
 */
class Simple extends \XLite\View\FormField\FileUploader\AFileUploader
{
    /**
     * Return 'isImage' flag
     *
     * @return boolean 
     */
    protected function isImage()
    {
        return false;
    }
}
