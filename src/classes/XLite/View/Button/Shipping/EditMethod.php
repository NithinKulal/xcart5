<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Shipping;

/**
 * Edit shipping method popup button
 */
class EditMethod extends \XLite\View\Button\APopupLink
{
    const PARAM_METHOD = 'method';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $shippingMarkups = new \XLite\View\ItemsList\Model\Shipping\Markups();
        $list = array_merge($list, $shippingMarkups->getCSSFiles());

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/shipping/edit_method.js';

        $shippingMarkups = new \XLite\View\ItemsList\Model\Shipping\Markups();
        $list = array_merge($list, $shippingMarkups->getJSFiles());

        return $list;
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
            static::PARAM_METHOD => new \XLite\Model\WidgetParam\TypeObject(
                'Shipping method',
                null,
                true,
                'XLite\Model\Shipping\Method'
            ),
        );
    }

    /**
     * Returns current shipping method
     *
     * @return \XLite\Model\Shipping\Method
     */
    protected function getMethod()
    {
        return $this->getParam(static::PARAM_METHOD);
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target' => 'shipping_rates',
            'widget' => 'XLite\View\Shipping\EditMethod',
            'methodId' => $this->getMethod()->getMethodId(),
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass() . ' edit-shipping-method-button';
        if ($this->getParam(static::PARAM_ICON_STYLE)) {
            $class .= ' icon';
        }

        return $class;
    }

    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getButtonContent()
    {
        return $this->getParam(static::PARAM_ICON_STYLE)
            ? ''
            : $this->getMethod()->getName();
    }
}
