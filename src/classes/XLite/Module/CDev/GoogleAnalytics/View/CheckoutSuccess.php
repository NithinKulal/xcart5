<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View;

use XLite\Module\CDev\GoogleAnalytics;

/**
 * Additional block for Checkout success page
 *
 * @Decorator\Depend ("CDev\DrupalConnector")
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
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoogleAnalytics/drupal.twig';
    }

    /**
     * Get account id from Drupal module
     *
     * @return string
     */
    protected function getAccount()
    {
        return variable_get('googleanalytics_account', '');
    }

    /**
     * Get commands for _gat
     *
     * @return array
     */
    protected function getGatCommands()
    {
        $list = array();

        $orders = \XLite\Core\Session::getInstance()->gaProcessedOrders;
        if (!is_array($orders)) {
            $orders = array();
        }

        /** @var \XLite\Model\Order $order */
        $order = $this->getOrder();
        if (!in_array($order->getOrderId(), $orders)) {
            $bAddress = $order->getProfile()->getBillingAddress();
            $city = $bAddress ? $bAddress->getCity() : '';
            $state = ($bAddress && $bAddress->getState()) ? $bAddress->getState()->getState() : '';
            $country = ($bAddress && $bAddress->getCountry()) ? $bAddress->getCountry()->getCountry() : '';

            $tax = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_TAX);
            $shipping = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);

            $list[] = '\'_addTrans\', '
                . '\'' . $order->getOrderNumber() . '\', '
                . '\'' . $this->escapeJavascript(\XLite\Core\Config::getInstance()->Company->company_name) . '\', '
                . '\'' . $order->getTotal() . '\', '
                . '\'' . $tax . '\', '
                . '\'' . $shipping . '\', '
                . '\'' . $this->escapeJavascript($city) . '\', '
                . '\'' . $this->escapeJavascript($state) . '\', '
                . '\'' . $this->escapeJavascript($country) . '\'';

            foreach ($order->getItems() as $item) {
                $product = $item->getProduct();
                $category = $product ? $product->getCategory() : null;
                if ($category && $category->getCategoryId()) {
                    $categories = \XLite\Core\Database::getRepo('XLite\Model\Category')
                        ->getCategoryPath($category->getCategoryId());
                    $category = array();
                    foreach ($categories as $cat) {
                        $category[] = $cat->getName();
                    }

                    $category = implode(' / ', $category);

                } else {
                    $category = '';
                }

                $list[] = '\'_addItem\', '
                    . '\'' . $order->getOrderId() . '\', '
                    . '\'' . $this->escapeJavascript($item->getSku()) . '\', '
                    . '\'' . $this->escapeJavascript($item->getName()) . '\', '
                    . '\'' . $this->escapeJavascript($category) . '\', '
                    . '\'' . $item->getPrice() . '\', '
                    . '\'' . $item->getAmount() . '\'';
            }

            $list[] = '\'_trackTrans\'';

            $orders[] = $order->getOrderId();
            \XLite\Core\Session::getInstance()->gaProcessedOrders = $orders;
        }

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !GoogleAnalytics\Main::useUniversalAnalytics()
            && $this->isDisplayDrupal();
    }

    /**
     * Display widget as Drupal-specific
     *
     * @return boolean
     */
    protected function isDisplayDrupal()
    {
        return \XLite\Module\CDev\DrupalConnector\Handler::getInstance()->checkCurrentCMS()
            && function_exists('googleanalytics_help')
            && $this->getAccount();
    }

    /**
     * Escape string for Javascript
     *
     * @param string $string String
     *
     * @return string
     */
    protected function escapeJavascript($string)
    {
        return strtr(
            $string,
            array(
                '\\' => '\\\\',
                '\'' => '\\\'',
                '"'  => '\\"',
                "\r" => '\\r',
                "\n" => '\\n',
                '</' =>'<\/'
            )
        );
    }
}
