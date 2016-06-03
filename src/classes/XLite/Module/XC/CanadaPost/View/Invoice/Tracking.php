<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\Invoice;

/**
 * Tracking
 *
 * @ListChild (list="invoice.bottom", weight="25", zone="customer")
 */
class Tracking extends \XLite\View\AView
{

    /**
     * Widget parameter names
     */

    const PARAM_ORDER = 'order';

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
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
            static::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order', null, false, '\XLite\Model\Order'
            ),
        );
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CanadaPost/order/invoice/parts/bottom.tracking.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getOrder()->getCapostTrackingPins();
    }
}
