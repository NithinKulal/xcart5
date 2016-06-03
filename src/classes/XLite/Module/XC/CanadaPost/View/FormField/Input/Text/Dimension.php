<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\FormField\Input\Text;

/**
 * Dimension
 *
 */
class Dimension extends \XLite\View\FormField\Input\Text\FloatInput
{
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_E]->setValue(1);
        $this->widgetParams[static::PARAM_MIN]->setValue(0);
        $this->widgetParams[static::PARAM_MAX]->setValue(999);
    }
}
