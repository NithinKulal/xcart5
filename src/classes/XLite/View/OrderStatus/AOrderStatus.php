<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\OrderStatus;

/**
 * Abstract order status
 */
abstract class AOrderStatus extends \XLite\View\AView
{
    /**
     * Widget parameter. Order.
     */
    const PARAM_ORDER       = 'order';

    /**
     * Widget parameter. Use wrapper flag.
     */
    const PARAM_USE_WRAPPER = 'useWrapper';

    /**
     * Return status
     *
     * @return mixed
     */
    abstract protected function getStatus();

    /**
     * Return label
     *
     * @return string
     */
    abstract protected function getLabel();

    /**
     * Check if the widget is visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->getOrder() && $this->getStatus();
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/order_status.twig';
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
            self::PARAM_ORDER       => new \XLite\Model\WidgetParam\TypeObject('Order', null, false, '\XLite\Model\Order'),
            self::PARAM_USE_WRAPPER => new \XLite\Model\WidgetParam\TypeBool('Use wrapper', false),
        );
    }

    /**
     * Return CSS class to use with wrapper
     *
     * @return string
     */
    protected function getCSSClass()
    {
        $code = $this->getStatus() ? $this->getStatus()->getCode() : '';

        return $code
            ? 'order-status-' . $code
            : 'order-status';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getTitle()
    {
        return $this->getStatus()
            ? (\XLite::isAdminZone() ? $this->getStatus()->getName() : $this->getStatus()->getCustomerName())
            : '';
    }
}
