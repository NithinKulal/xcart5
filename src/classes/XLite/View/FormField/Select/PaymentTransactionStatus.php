<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Payment transaction status
 */
class PaymentTransactionStatus extends \XLite\View\FormField\Select\Regular
{

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $statuses = \XLite\Model\Payment\Transaction::getStatuses();

        $options = array(
            ''  => static::t('All payment transaction statuses')
        );

        foreach ($statuses as $key => $status) {
            $options[$key] = static::t($status . '[S]');
        }

        return $options;
    }

}
