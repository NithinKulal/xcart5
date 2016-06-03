<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Model;

/**
 * Abstract class for Paypal settings form
 */
abstract class ASettings extends \XLite\View\Model\AModel
{
    const PARAM_PAYMENT_METHOD = 'paymentMethod';

    /**
     * Form sections
     */
    const SECTION_ACCOUNT    = 'account';
    const SECTION_ADDITIONAL = 'additional';

    /**
     * Schema of the "Your account settings" section
     *
     * @var array
     */
    protected $schemaAccount = array(
        'partner' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Partner name',
            self::SCHEMA_HELP     => 'Your partner name is PayPal',
            self::SCHEMA_REQUIRED => true,
        ),
        'vendor' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Merchant login',
            self::SCHEMA_HELP     => 'This is the login name you created when signing up for PayPal Payments Advanced.',
            self::SCHEMA_REQUIRED => true,
        ),
        'user' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'User',
            self::SCHEMA_HELP     => 'PayPal recommends entering a User Login here instead of your Merchant Login',
            self::SCHEMA_REQUIRED => true,
        ),
        'pwd' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Password',
            self::SCHEMA_HELP     => 'This is the password you created when signing up for PayPal Payments Advanced or the password you created for API calls.',
            self::SCHEMA_REQUIRED => true,
        ),
    );

    /**
     * Schema of the "Additional settings" section
     *
     * @var array
     */
    protected $schemaAdditional = array(
        'transaction_type' => array(
            self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\TransactionType',
            self::SCHEMA_LABEL    => 'Transaction type',
            self::SCHEMA_REQUIRED => false,
        ),
        'mode' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\TestLiveMode',
            self::SCHEMA_LABEL    => 'Test/Live mode',
            self::SCHEMA_REQUIRED => false,
        ),
        'prefix' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Order id prefix',
            self::SCHEMA_HELP     => 'You can define an order id prefix, which would precede each order number in your shop, to make it unique',
            self::SCHEMA_REQUIRED => false,
        ),
        'buyNowEnabled' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Display the "Buy Now with PayPal" button',
            self::SCHEMA_HELP     => 'This setting determines whether or not the "Buy Now with PayPal" button should be displayed on product list pages (in list view) and product details pages.',
            self::SCHEMA_REQUIRED => false,
        ),
    );

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Paypal/settings/payments_style.css';

        return $list;
    }

    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        $this->sections = $this->getSettingsSections() + $this->sections;

        parent::__construct($params, $sections);
    }

    /**
     * Return model object to use
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getModelObject()
    {
        return $this->getPaymentMethod();
    }

    /**
     * Return list of the class-specific sections
     *
     * @return array
     */
    protected function getSettingsSections()
    {
        return array(
            static::SECTION_ACCOUNT    => 'Your account settings',
            static::SECTION_ADDITIONAL => 'Additional settings',
        );
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\CDev\Paypal\View\Form\Settings';
    }

    /**
     * There is no object for settings
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDefaultModelObject()
    {
        return null;
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        $paymentMethod = $this->getParam(static::PARAM_PAYMENT_METHOD);

        return $paymentMethod
            ? $paymentMethod->getSetting($name)
            : null;
    }

    /**
     * defineWidgetParams
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PAYMENT_METHOD => new \XLite\Model\WidgetParam\TypeObject('Payment method', null),
        );
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        return 'XLite\View\StickyPanel\Payment\Settings';
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        foreach ($data as $name => $value) {
            $this->getModelObject()->setSetting($name, $value);
        }
    }
}
