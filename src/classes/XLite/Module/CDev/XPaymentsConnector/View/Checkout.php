<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View;

/**
 * Checkout
 *
 */
abstract class Checkout extends \XLite\View\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ('1.1' == \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_api_version) {
            $list[] = 'modules/CDev/XPaymentsConnector/checkout/style_old.css';

        } else {
            $list[] = 'modules/CDev/XPaymentsConnector/checkout/style.css';
            $list[] = 'modules/CDev/XPaymentsConnector/checkout/popover/jquery.webui-popover.css';
        }

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

        if ('1.1' == \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_api_version) {
            $list[] = 'modules/CDev/XPaymentsConnector/checkout/script_old.js';

        } else {
            $list[] = 'modules/CDev/XPaymentsConnector/iframe_common.js';
            $list[] = 'modules/CDev/XPaymentsConnector/checkout/script.js';
            $list[] = 'modules/CDev/XPaymentsConnector/checkout/popover/jquery.webui-popover.js';
        }

        return $list;
    }

}
