<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View;

/**
 * Config
 */
class Config extends \XLite\View\AView
{
    /**
     * Get payment method
     *
     * @return  \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        return \XLite\Module\XC\Pilibaba\Main::getPaymentMethod();
    }

    /**
     * Get payment method information url
     *
     * @return string
     */
    public function getInformationURL()
    {
        return $this->getPaymentMethod() && $this->getPaymentMethod()->getSetting('mode') === 'test'
            ? 'http://preen.pilibaba.com'
            : 'http://en.pilibaba.com';
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Pilibaba/config.css';
        $list[] = 'modules/XC/Pilibaba/info_block.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Pilibaba/config.twig';
    }

    /**
     * Return widget default template
     *
     * @param string $mode Payment method mode, can be 'test' or 'live'
     * @return string
     */
    public function getPilibabaAccountUrl($paymentMethodMode)
    {
        $host = $paymentMethodMode === 'test'
            ? 'preen.pilibaba.com'
            : 'en.pilibaba.com';

        return 'http://' . $host . '/account/myAccount';
    }
}
