<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\View;

/**
 * Payment widget
 */
class Payment extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Stripe/checkout.twig';
    }

    /**
     * Get data attributes
     *
     * @return array
     */
    protected function getDataAttributes()
    {
        $total = $this->getCart()->getCurrency()->roundValue(
            $this->getCart()->getFirstOpenPaymentTransaction()->getValue()
        );

        $method = $this->getCart()->getPaymentMethod();
        $suffix = $method->getProcessor()->isTestMode($method) ? 'Test' : '';
        $description = static::t(
            'X items ($)',
            [
                'count' => $this->getCart()->countQuantity(),
                'total' => $this->formatPrice($total, $this->getCart()->getCurrency()),
            ]
        );

        $data = [
            'data-key'         => $this->getCart()->getPaymentMethod()->getSetting('publishKey' . $suffix),
            'data-name'        => \XLite\Core\Config::getInstance()->Company->company_name,
            'data-description' => $description,
            'data-total'       => $this->getCart()->getCurrency()->roundValueAsInteger($total),
            'data-currency'    => $this->getCart()->getCurrency()->getCode(),
            'data-locale'      => $this->getPreparedLanguageCode(),
        ];

        if (\XLite\Core\Session::getInstance()->checkoutEmail) {
            $data['data-email'] = \XLite\Core\Session::getInstance()->checkoutEmail;

        } elseif ($this->getCart()->getProfile()) {
            $data['data-email'] = $this->getCart()->getProfile()->getLogin();
        }

        return $data;
    }

    /**
     * @return string
     */
    protected function getPreparedLanguageCode()
    {
        $code = \XLite\Core\Session::getInstance()->getCurrentLanguage();

        if ($code === 'gb') {
            return 'en';
        }

        return $code ?: 'auto';
    }
}

