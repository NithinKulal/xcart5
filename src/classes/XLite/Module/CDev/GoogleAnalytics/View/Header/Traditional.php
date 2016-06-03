<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View\Header;

/**
 * Header declaration (Traditional)
 *
 * @ListChild (list="head")
 */
class Traditional extends \XLite\Module\CDev\GoogleAnalytics\View\Header\AHeader
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoogleAnalytics/header/traditional.twig';
    }

    /**
     * Get _gaq options list
     *
     * @return array
     */
    protected function getGaqOptions()
    {
        $list = array(
            sprintf('\'_setAccount\', \'%s\'', \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_account)
        );

        if (2 == \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_tracking_type) {
            $list[] = '\'_setDomainName\', \'.\' + self.location.host.replace(/^[^\.]+\./, \'\')';

        } elseif (3 == \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_tracking_type) {
            $list[] = '\'_setDomainName\', \'none\'';
            $list[] = '\'_setAllowLinker\', true';
        }

        $list[] = '\'_trackPageview\'';

        $controller = \XLite::getController();

        if ($this->isEcommercePartEnabled() && $controller instanceof \XLite\Controller\Customer\CheckoutSuccess) {
            $orders = \XLite\Core\Session::getInstance()->gaProcessedOrders;
            if (!is_array($orders)) {
                $orders = array();
            }

            $order = $this->getOrder();

            if ($order->getProfile()
                && !in_array($order->getOrderId(), $orders)
            ) {
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
                    $list[] = '\'_addItem\', '
                        . '\'' . $order->getOrderNumber() . '\', '
                        . '\'' . $this->escapeJavascript($item->getSku()) . '\', '
                        . '\'' . $this->escapeJavascript($item->getName()) . '\', '
                        . '\'\', '
                        . '\'' . $item->getPrice() . '\', '
                        . '\'' . $item->getAmount() . '\'';
                }

                $list[] = '\'_trackTrans\'';

                $orders[] = $order->getOrderId();
                \XLite\Core\Session::getInstance()->gaProcessedOrders = $orders;
            }
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
            && !$this->useUniversalAnalytics();
    }
}
