<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * 'Print invoice' button widget
 *
 * @ListChild (list="page.tabs.after", zone="admin", weight="100")
 */
class PrintPackingSlip extends \XLite\View\Button\AButton
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $targets = parent::getAllowedTargets();
        $targets[] = 'order';

        if (!\XLite::isAdminZone()) {
            $targets[] = 'checkoutSuccess';
        }

        return $targets;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/print_invoice.js';

        return $list;
    }

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'button/css/print_invoice.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/print_invoice.twig';
    }

    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'button print-invoice';
    }

    /**
     * Get default label
     * todo: move translation here
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Print packing slip';
    }

    /**
     * Returns current order
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return \XLite::getController()->getOrder();
    }

    /**
     * Return URL params to use with onclick event
     *
     * @return array
     */
    protected function getURLParams()
    {
        return array(
            'url_params' => array (
                'target'       => 'order',
                'order_number' => $this->getOrder()->getOrderNumber(),
                'mode'         => 'packing_slip',
            ),
        );
    }
}
