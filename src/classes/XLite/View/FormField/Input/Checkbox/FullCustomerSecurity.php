<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;


class FullCustomerSecurity extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/input/checkbox/full_customer_security.js';

        return $list;
    }

    /**
     * Return forced widget parameters list
     *
     * @return \XLite\Model\WidgetParam\AWidgetParam[]
     */
    protected function getForcedWidgetParams()
    {
        return [
            self::PARAM_ON_LABEL => new \XLite\Model\WidgetParam\TypeString('On label', static::t('Yes')),
            self::PARAM_OFF_LABEL => new \XLite\Model\WidgetParam\TypeString('Off label', static::t('No')),
            self::PARAM_LABEL => new \XLite\Model\WidgetParam\TypeString('Label', static::t('Redirect customers to HTTPS')),
        ];
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        foreach ($this->getForcedWidgetParams() as $key => $param) {
            $this->widgetParams[$key] = $param;
        }
    }

    /**
     * Register CSS class to use for wrapper block of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' full-customer-security-wrapper';
    }

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return (boolean)\XLite\Core\Config::getInstance()->Security->force_customers_to_https;
    }
}