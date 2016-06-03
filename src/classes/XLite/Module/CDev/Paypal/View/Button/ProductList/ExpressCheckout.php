<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\ProductList;

/**
 * Express Checkout button
 */
class ExpressCheckout extends \XLite\Module\CDev\Paypal\View\Button\AExpressCheckout
{
    const PARAM_PRODUCT_ID = 'productId';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PRODUCT_ID => new \XLite\Model\WidgetParam\TypeInt('Product id'),
        );
    }

    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Module\CDev\Paypal\Main::isBuyNowEnabled();
    }

    /**
     * Returns widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/button/product_list/default/express_checkout.twig';
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->isInContextAvailable()
            ? 'modules/CDev/Paypal/button/product_list/in_context/express_checkout.twig'
            : 'modules/CDev/Paypal/button/product_list/default/express_checkout.twig';
    }

    /**
     * Returns additional link params
     *
     * @return array
     */
    protected function getAdditionalLinkParams()
    {
        $result = parent::getAdditionalLinkParams();

        if ($this->isInContextAvailable()) {
            $result['inContext'] = true;
            $result['cancelUrl'] = $this->isAjax()
                ? $this->getReferrerURL()
                : \XLite\Core\URLManager::getSelfURI();
        }

        $result['product_id'] = $this->getParam(static::PARAM_PRODUCT_ID);
        $result['expressCheckout'] = true;

        return $result;
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        $params = $this->getAdditionalLinkParams();

        $url = $this->buildURL('cart', 'add', $params);

        return \XLite::getInstance()->getShopURL($url);
    }
}
