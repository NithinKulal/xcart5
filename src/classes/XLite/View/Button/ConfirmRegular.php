<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Regular button with js confirm
 */
class ConfirmRegular extends \XLite\View\Button\Regular
{
    /**
     * Widget parameter names
     */
    const PARAM_CONFIRM_TEXT      = 'confirmText';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_CONFIRM_TEXT    => new \XLite\Model\WidgetParam\TypeString('Confirm text', static::t('Are you sure?')),
        );
    }

    /**
     * Return specified (or default) JS code with confirmation
     *
     * @return string
     */
    protected function getJSCode()
    {
        return sprintf(
            'if (confirm("%s")) { %s };',
            $this->getParam(self::PARAM_CONFIRM_TEXT),
            parent::getJSCode()
        );
    }
}
