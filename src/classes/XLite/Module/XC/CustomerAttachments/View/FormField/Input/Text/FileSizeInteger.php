<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\View\FormField\Input\Text;

/**
 * Field for file size
 */
class FileSizeInteger extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Widget param
     */
    const PARAM_VALUE_E = 4;

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_COMMENT]->setValue($this->getFileSizeComment());
        $this->widgetParams[static::PARAM_E]->setValue(static::PARAM_VALUE_E);
    }

    /**
     * Get human readable file size comment
     *
     * @return string
     */
    protected function getFileSizeComment()
    {
        return \XLite\Core\Translation::lbl(
            'Web server max upload file size limit is X',
            array('size' => \XLite\Core\Converter::convertShortSizeToHumanReadable(ini_get('upload_max_filesize')))
        );
    }
} 