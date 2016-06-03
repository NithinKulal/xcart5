<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
