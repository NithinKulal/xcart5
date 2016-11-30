<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Code widget
 */
class Code extends \XLite\Module\XC\ThemeTweaker\View\Custom
{
    const PARAM_TYPE = 'type';

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir(). '/code';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_TYPE => new \XLite\Model\WidgetParam\TypeString(
                'Code widget type', null, false
            ),
        );
    }

    /**
     * Returns widget type
     * @return string
     */
    protected function getType() 
    {
        return $this->getParam(static::PARAM_TYPE) ?: \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Code is used or not
     *
     * @return boolean
     */
    protected function isUsed()
    {
        return (boolean) \XLite\Core\Config::getInstance()->XC->ThemeTweaker->{'use_' . $this->getType()};
    }

    /**
     * Return custom text
     *
     * @return boolean
     */
    protected function getUseCustomText()
    {
        return 'custom_css' == $this->getType()
            ? static::t('Use custom css')
            : static::t('Use custom js');
    }
}
