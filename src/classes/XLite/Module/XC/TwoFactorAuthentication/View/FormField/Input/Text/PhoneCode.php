<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View\FormField\Input\Text;

/**
 * Integer
 */
class PhoneCode extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return parent::prepareRequestData($value) ?: '';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_MOUSE_WHEEL_CTRL] = new \XLite\Model\WidgetParam\TypeBool('Mouse wheel control', false);
        $this->widgetParams[static::PARAM_MOUSE_WHEEL_ICON] = new \XLite\Model\WidgetParam\TypeBool('User mouse wheel icon', false);
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 5;
    }
}