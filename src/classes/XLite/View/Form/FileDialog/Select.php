<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\FileDialog;

/**
 * File select dialog form
 */
class Select extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'select_file';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'select';
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return 'file-select-form';
    }

    /**
     * Ability to add the 'enctype="multipart/form-data"' form attribute
     *
     * @return boolean
     */
    protected function isMultipart()
    {
        return true;
    }
}
