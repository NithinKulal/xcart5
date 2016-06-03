<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Checkout success page
 *
 * @ListChild (list="center")
 */
class CheckoutSuccess extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'checkoutSuccess';

        return $list;
    }


    /**
     * Get continue URL
     *
     * @return string
     */
    public function getContinueURL()
    {
        if (\XLite\Core\Session::getInstance()->continueShoppingURL) {
            $url = $this->getURL(\XLite\Core\Session::getInstance()->continueShoppingURL);

        } elseif (isset($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];

        } else {
            $url = $this->buildURL();
        }

        return $url;
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/success.twig';
    }
}
