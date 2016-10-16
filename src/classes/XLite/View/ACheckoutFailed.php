<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Checkout failed page
 *
 */
abstract class ACheckoutFailed extends \XLite\View\AView
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'checkout/css/checkout.css';

        return $list;
    }

    /**
     * Get continue URL
     *
     * @return string
     */
    protected function getContinueURL()
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
     * Get Re-order URL
     *
     * @return string
     */
    protected function getReorderURL()
    {
        return $this->buildURL('cart', 'add_order', array('order_number' => $this->getOrder()->getOrderNumber()));
    }

    /**
     * Get failure reason
     *
     * @return string
     */
    protected function getFailureReason()
    {
        return $this->getOrder()
            ? $this->getOrder()->getFailureReason()
            : null;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/failed.twig';
    }

    /**
     * Return failed template
     *
     * @return string
     */
    abstract protected function getFailedTemplate();
}
