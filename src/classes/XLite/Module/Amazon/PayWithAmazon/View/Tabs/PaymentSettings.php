<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Tabs;

/**
 * Tabs related to payment settings
 */
abstract class PaymentSettings extends \XLite\View\Tabs\PaymentSettings implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'pay_with_amazon';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        $list['pay_with_amazon'] = [
            'weight' => 300,
            'title'  => static::t('Pay with Amazon'),
            'template' => 'modules/Amazon/PayWithAmazon/configuration.twig',
        ];

        return $list;
    }

    /**
     * @return boolean
     */
    protected function hasAmazonWarning()
    {
        $api = \XLite\Module\Amazon\PayWithAmazon\Main::getApi();

        return !$api->isConfigured();
    }

    /**
     * @return string
     */
    protected function getAmazonWarning()
    {
        $api = \XLite\Module\Amazon\PayWithAmazon\Main::getApi();

        if (!$api->isConfigured() && \XLite\Core\Config::getInstance()->Security->customer_security) {

            return static::t('The "Pay With Amazon" feature is not configured and cannot be used.');

        } elseif (!\XLite\Core\Config::getInstance()->Security->customer_security) {

            return static::t(
                'The "Pay with Amazon" feature requires https to be properly set up for your store.',
                [
                    'url' => $this->buildURL('https_settings')
                ]
            );
        }

        return '';
    }
}
