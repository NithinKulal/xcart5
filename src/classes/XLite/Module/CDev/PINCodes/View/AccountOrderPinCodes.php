<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View;

/**
 * Account pin codes page order block
 */
class AccountOrderPinCodes extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_ORDER = 'order';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/PINCodes/accountPinCodes/style.css';

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
        $list[] = 'modules/CDev/PINCodes/accountPinCodes/script.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/PINCodes/accountPinCodes/order.twig';
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
            self::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject('Order', null, false, '\\XLite\\\Model\\Order'),
        );
    }

    /**
     * getOrderUrl 
     *
     * @return string
     */
    protected function getOrderUrl()
    {
        return $this->buildUrl('order', '', array('order_number' => $this->getParam(self::PARAM_ORDER)->getOrderNumber()));
    }

}
