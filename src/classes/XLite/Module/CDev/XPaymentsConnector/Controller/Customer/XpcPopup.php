<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 namespace XLite\Module\CDev\XPaymentsConnector\Controller\Customer;

/**
 * Saved credit cards 
 */
class XpcPopup extends \XLite\Controller\Customer\ACustomer
{

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_DO_NOTHING == $this->getType()
            ? static::t('X-Payments info')
            : static::t('X-Payments error');
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return \XLite\Core\Request::getInstance()->widget;
    }

    /**
     * Get message 
     *
     * @return string
     */
    public function getMessage()
    {
        $message = \XLite\Model\Payment\Transaction::getDefaultFailedReason();

        if (\XLite\Core\Request::getInstance()->message) {

            $message = urldecode(\XLite\Core\Request::getInstance()->message);
        }

        return $message;
    }

    /**
     * Display the default error message to this user or not (method not used currently)
     *
     * @return bool
     */
    public function isDisplayDefaultMessageForThisUser()
    {
        list($code, $message) = \XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::getInstance()
            ->parseErrorMessage(urldecode(\XLite\Core\Request::getInstance()->message));

        $result = false;

        if (
            '505' == $code
            && !$this->isAdminUser()
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Check if this is error for unaccepted changes of templates in X-Payments
     *
     * @param string $message Message
     *
     * @return array
     */
    public function isUnacceptedTemplateError($message)
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::getInstance()->isUnacceptedTemplateError($message);
    }

    /**
     * Get Contact Us link 
     *
     * @return string
     */
    public function getContactUsLink()
    {
        return $this->buildURL('contact_us');
    }

    /**
     * Get link to the manual 
     *
     * @param string $code Error code
     *
     * @return string
     */
    public function getManualLink($code)
    {
        $link = '';

        switch ($code) {
            case '505': 
                $link = 'http://help.x-cart.com/index.php?title=X-Payments:Troubleshooting#Error_message_at_checkout_instead_of_credit_card_form';
                break;
            default:
                break;
        }

        return $link;
    }

    /**
     * Get title of the manual article
     *
     * @param string $code Error code
     *
     * @return string
     */
    public function getManualTitle($code)
    {
        $title = '';

        switch ($code) {
            case '505':
                $title = 'Error message at checkout instead of credit card form';
                break;
            default:
                break;
        }

        return $title;
    }

    /**
     * Is this admin user
     *
     * @return boolean
     */
    public function isAdminUser()
    {
        return \XLite\Core\Auth::getInstance()->isAdmin();
    }

    /**
     * Get link to X-Payments dashboard
     *
     * @return string
     */
    public function getDashboardLink()
    {
        return \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_xpayments_url . 'admin.php';
    }

    /**
     * Checkout. Recognize iframe and save that
     *
     * @return integer
     */
    public function getType()
    {
        $type = \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_DO_NOTHING;

        $request = \XLite\Core\Request::getInstance();

        if (
            $request->type
            && is_numeric($request->type)
        ) {
            $type = intval($request->type);

            if (
                $type > \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_MAX_ACTION 
                || $type < 0
            ) {
                $type = \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_DO_NOTHING;
            }
        }

        return $type; 
    }

    /**
     * Get non X-Payments payment method ID
     *
     * @return int
     */
    protected function getNonXpcPaymentMethodId()
    {
        $methods = $this->getCart()->getPaymentMethods();

        $methodId = false;

        foreach ($methods as $method) {

            if ($method->getClass() != \XLite\Module\CDev\XPaymentsConnector\Model\Payment\Processor\AXPayments::METHOD_XPAYMENTS) {
                $methodId = $method->getMethodId();
                break;
            }
        }

        if (!$methodId) {
            $methodId = getNextPaymentMethodId();
        }

        return $methodId;
    }

    /**
     * Get next payment method ID
     *
     * @return int
     */
    protected function getNextPaymentMethodId()
    {
        $methods = $this->getCart()->getPaymentMethods();

        $selectedMethod = $this->getCart()->getPaymentMethod();

        $next = $nextMethodId = $firstMethodId = false;

        // Methods/functions like next() and getNext() do not work
        // so we do something strange to run across array
        foreach ($methods as $method) {

            if (!$firstMethodId) {
                $firstMethodId = $method->getMethodId();
            }

            if ($next) {
                $nextMethodId = $method->getMethodId();
                break;
            }

            if ($selectedMethod->getMethodId() == $method->getMethodId()) {
                $next = true;
            }
        }

        if (!$nextMethodId) {
            $nextMethodId = $firstMethodId;
        }

        return $nextMethodId;
    }

    /**
     * Get button action
     *
     * @return string
     */
    public function getButtonAction()
    {
        $result = '';

        switch ($this->getType()) {

            case \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_CHANGE_METHOD:

                $methodId = $this->isUnacceptedTemplateError(urldecode(\XLite\Core\Request::getInstance()->message))
                    ? $this->getNonXpcPaymentMethodId()
                    : $this->getNextPaymentMethodId();

                $result = 'window.location.href = "' . $this->buildUrl('checkout', 'payment', array('methodId' => $methodId)) . '";';
                break;

            case \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_CLEAR_INIT_DATA:
                $result = 'window.location.href = "' . $this->buildUrl('checkout', 'clear_init_data') . '";';
                break;

            case \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_DO_NOTHING:
            default:
                break;
        }

        return $result;
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return '';
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode('X-Payments');
    }

}
