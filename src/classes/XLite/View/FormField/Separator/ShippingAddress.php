<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Separator;

/**
 * \XLite\View\FormField\Separator\ShippingAddress
 */
class ShippingAddress extends \XLite\View\FormField\Separator\ASeparator
{
    /**
     * Widget param names
     */

    const PARAM_SHIP_AS_BILL_CHECKBOX = 'shipAsBillCheckbox';


    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/shipping_address.css';

        return $list;
    }


    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'shipping_address.twig';
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
            self::PARAM_SHIP_AS_BILL_CHECKBOX => new \XLite\Model\WidgetParam\TypeObject(
                '"Ship as bill" checkbox', null, false, '\XLite\View\FormField\Input\Checkbox\ShipAsBill'
            ),
        );
    }

    /**
     * Show the "Ship as bill" checkbox
     *
     * @return void
     */
    protected function showShipAsBillCheckbox()
    {
        return $this->getParam(self::PARAM_SHIP_AS_BILL_CHECKBOX)->getContent();
    }
}
