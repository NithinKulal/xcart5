<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\Settings;

/**
 * Settings payment methods
 */
class PaymentMethods extends \XLite\Module\CDev\XPaymentsConnector\View\Settings\ASettings
{

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/payment_methods/list.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Module\CDev\XPaymentsConnector\Core\Settings::hasPaymentMethods();
    }

    /**
     * List of tabs/pages where this setting should be displayed
     *
     * @return boolean
     */
    public function getPages()
    {
        return array(\XLite\Module\CDev\XPaymentsConnector\Core\Settings::PAGE_PAYMENT_METHODS);
    }
}
