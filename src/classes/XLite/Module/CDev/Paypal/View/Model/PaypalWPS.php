<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Model;

/**
 * PaypalWPS
 */
class PaypalWPS extends \XLite\Module\CDev\Paypal\View\Model\ASettings
{
    /**
     * Schema of the "Your account settings" section
     *
     * @var array
     */
    protected $schemaAccount = array(
        'account' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'PayPal ID / Email',
            self::SCHEMA_HELP     => 'Enter the email address associated with your PayPal account.',
            self::SCHEMA_REQUIRED => true,
        ),
    );

    /**
     * Schema of the "Additional settings" section
     *
     * @var array
     */
    protected $schemaAdditional = array(
        'description' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Purchase description',
            self::SCHEMA_HELP     => 'Enter description of the purchase that will be displayed on PayPal payment page.',
            self::SCHEMA_REQUIRED => true,
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
    );
}
