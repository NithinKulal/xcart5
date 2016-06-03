<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * More addons link
 */
class MoreAddons extends \XLite\View\Button\Link
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */    
    protected function isVisible()
    {
        return parent::isVisible() 
            && 'addons_list_installed' === \XLite\Core\Request::getInstance()->target
            && !isset(\XLite\Core\Request::getInstance()->recent);
    }
    
    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_LOCATION]->setValue($this->buildURL('addons_list_marketplace'));
        $this->widgetParams[self::PARAM_LABEL]->setValue('More add-ons');
        $this->widgetParams[self::PARAM_STYLE]->setValue('more-addons-button');
    }
}
