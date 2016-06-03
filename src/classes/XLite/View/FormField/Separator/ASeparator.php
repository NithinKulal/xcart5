<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Separator;


/**
 * \XLite\View\FormField\Separator\ASeparator
 */
abstract class ASeparator extends \XLite\View\FormField\AFormField
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_SEPARATOR;
    }


    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/separator';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/' . $this->getFieldTemplate();
    }

    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return 'separator';
    }
}
