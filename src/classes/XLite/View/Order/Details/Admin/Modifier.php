<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin;

/**
 * Order modifier widget
 */
class Modifier extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_ORDER          = 'order';
    const PARAM_SURCHARGE      = 'surcharge';
    const PARAM_SURCHARGE_TYPE = 'sType';


    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/page/parts/totals.modifier.default.twig';
    }

    /**
     * Define widget parameters
     *
     * @return array
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ORDER          => new \XLite\Model\WidgetParam\TypeObject(
                'Order', null, false, '\XLite\Model\Order'
            ),
            self::PARAM_SURCHARGE      => new \XLite\Model\WidgetParam\TypeCollection(
                'Order surcharge', array(), false, '\XLite\Model\Order\Surcharge'
            ),
            self::PARAM_SURCHARGE_TYPE => new \XLite\Model\WidgetParam\TypeString(
                'Surcharge type', '', false
            ),
        );
    }

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
     * Get surcharge
     *
     * @return \XLite\Model\Order\Surcharge
     */
    protected function getSurcharge()
    {
        return $this->getParam(self::PARAM_SURCHARGE);
    }

    /**
     * Get surcharge type
     *
     * @return string
     */
    protected function getSurchargeType()
    {
        return $this->getParam(self::PARAM_SURCHARGE_TYPE);
    }

    /**
     * Format surcharge value
     *
     * @param array $surcharge Surcharge
     *
     * @return string
     */
    protected function formatSurcharge(array $surcharge)
    {
        return $this->formatPrice(abs($surcharge['cost']), $this->getOrder()->getCurrency(), !\XLite::isAdminZone());
    }

}
