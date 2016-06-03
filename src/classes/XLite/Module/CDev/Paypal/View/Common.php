<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

/**
 * Common widget extention.
 * This widget is used only to link additional css and js files to the page
 *
 * @Decorator\Depend ("XC\Add2CartPopup")
 */
class Common extends \XLite\Module\XC\Add2CartPopup\View\Common implements \XLite\Base\IDecorator
{
    /**
     * Add CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $cart = $this->getCart();
        if (\XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled($cart)) {
            $list[] = 'modules/CDev/Paypal/button/add2cart_popup/style.css';
        }

        return $list;
    }
}
