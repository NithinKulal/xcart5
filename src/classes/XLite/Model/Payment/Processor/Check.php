<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment\Processor;

/**
 * E-check
 */
class Check extends \XLite\Model\Payment\Processor\Offline
{
    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return 'checkout/echeck.twig';
    }

    /**
     * Check - display check number or not
     *
     * @return boolean
     */
    public function isDisplayNumber()
    {
        return \XLite\Core\Config::getInstance()->General->display_check_number;
    }


    /**
     * Get input data labels list
     *
     * @return array
     */
    protected function getInputDataLabels()
    {
        return array(
            'check_routing_number' => 'ABA routing number',
            'check_acct_number'    => 'Bank Account Number',
            'check_type'           => 'Type of Account',
            'check_bank_name'      => 'Bank name',
            'check_acct_name'      => 'Name of account holder',
            'check_number'         => 'Check number',
        );
    }

    /**
     * Get input data access levels list
     *
     * @return array
     */
    protected function getInputDataAccessLevels()
    {
        return array(
            'check_routing_number' => \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
            'check_acct_number'    => \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
            'check_type'           => \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
            'check_bank_name'      => \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
            'check_acct_name'      => \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
            'check_number'         => \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
        );
    }
}
