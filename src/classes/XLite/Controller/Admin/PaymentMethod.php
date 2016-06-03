<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Payment method
 */
class PaymentMethod extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var string
     */
    protected $params = array('target', 'method_id');

    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getPaymentMethod()
            ? static::t('{{paymentMethod}} settings', array('paymentMethod' => $this->getPaymentMethod()->getName()))
            : static::t('Payment method settings');
    }


    /**
     * getPaymentMethod
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')
            ->find(\XLite\Core\Request::getInstance()->method_id);
    }

    /**
     * Update payment method
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $settings = \XLite\Core\Request::getInstance()->settings;
        $method = $this->getPaymentMethod();

        if (!$method) {
            \XLite\Core\TopMessage::addError('An attempt to update settings of unknown payment method');

        } else {
            if (is_array($settings)) {
                foreach ($settings as $name => $value) {
                    $method->setSetting($name, trim($value));
                }
            }

            $properties = \XLite\Core\Request::getInstance()->properties;
            if (is_array($properties) && !empty($properties)) {
                $method->map($properties);
            }

            \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')->update($method);

            // If "just added" method is configured and can be enabled then we enable it
            if (\XLite\Core\Request::getInstance()->just_added && $method->isConfigured() && $method->canEnable()) {
                $method->setEnabled(true);
                \XLite\Core\Database::getEM()->flush();
            }
            if ($method->isConfigured()) {
                \XLite\Core\TopMessage::addInfo('The settings of payment method successfully updated');
                $this->setReturnURL($this->buildURL('payment_settings'));
            } else {
                \XLite\Core\TopMessage::addWarning('Payment method has not been configured properly');
            }
        }
    }
}
