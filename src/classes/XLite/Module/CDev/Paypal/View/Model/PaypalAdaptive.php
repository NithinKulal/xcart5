<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Model;

/**
 * PaypalAdvanced
 */
class PaypalAdaptive extends \XLite\Module\CDev\Paypal\View\Model\ASettings
{
    /**
     * Schema of the "Your account settings" section
     *
     * @var array
     */
    protected $schemaAccount = array(
        'app_id' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Application ID',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ),
        'paypal_login' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Paypal login (email)',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true
        ),
        'api_username' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'API access username',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ),
        'api_password' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'API access password',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ),
        'signature' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'API signature',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ),
        'feesPayer' => array(
            self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\FeesPayer',
            self::SCHEMA_LABEL    => 'Fees payer',
            self::SCHEMA_HELP     => 'See more details here <a href="https://developer.paypal.com/docs/classic/adaptive-payments/integration-guide/APIntro/#id091QF0N0MPF">https://developer.paypal.com/docs/classic/adaptive-payments/integration-guide/APIntro/#id091QF0N0MPF</a>',
            self::SCHEMA_REQUIRED => true,
        ),
    );

    /**
     * Schema of the "Additional settings" section
     *
     * @var array
     */
    protected $schemaAdditional = array(
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
