<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View;

/**
 * Product tab page view
 *
 */
class Tab extends \XLite\View\AView
{
    const PARAM_TAB      = 'tab';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CustomProductTabs/tab.twig';
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
            self::PARAM_TAB     => new \XLite\Model\WidgetParam\TypeObject(
                'Tab',
                null,
                false,
                '\XLite\Module\XC\CustomProductTabs\Model\Product\Tab'
            ),
        );
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Product
     */
    protected function getTab()
    {
        return $this->getParam(self::PARAM_TAB);
    }
}