<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Addon;

/**
 * Purchase module button-link
 *
 */
class Purchase extends \XLite\View\Button\AButton
{
    const PARAM_MODULE = 'moduleObj';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/addon/purchase.twig';
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
            self::PARAM_MODULE => new \XLite\Model\WidgetParam\TypeObject('Module', null, false, '\XLite\Model\Module'),
        );
    }

    /**
     * Return button text
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return 'Purchase';
    }

    /**
     * Define the button type (btn-warning and so on)
     *
     * @return string
     */
    protected function getDefaultButtonType()
    {
        return 'regular-main-button';
    }

    /**
     * Return button CSS class
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' purchase-module';
    }

    /**
     * Get JS code
     *
     * @return string
     */
    protected function getJSCode()
    {
        return 'onclick="javascript:self.location=\''
            . \XLite\Core\Marketplace::getPurchaseURL($this->getParam(static::PARAM_MODULE)->getXbProductId())
            . '\'"';
    }
}
