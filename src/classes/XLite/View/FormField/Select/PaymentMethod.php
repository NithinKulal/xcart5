<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Payment method
 */
class PaymentMethod extends \XLite\View\FormField\Select\Regular
{
    /**
     * Deleted key code
     */
    const KEY_DELETED = 'deleted';
    const KEY_NONE    = 'none';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $result = array();
        $list = array();

        // Get all active payment methods
        $methods = \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')->findAllActive();

        // In case methods have custom isEnabled() method
        $activeMethods = array_filter(
            $methods,
            function($method) {
                return $method->isEnabled();
            }
        );

        foreach ($activeMethods as $method) {
            $list[$method->getMethodId()] = $method->getTitle();
        }

        if ($this->getOrder() && $this->getOrder()->getPaymentTransactions()) {

            // Get current order payment method
            foreach ($this->getOrder()->getPaymentTransactions() as $t) {
                $savedMethod = $t->getPaymentMethod()
                    ? $t->getPaymentMethod()->getTitle()
                    : $t->getMethodLocalName();

                if ($savedMethod && !array_search($savedMethod, $list)) {
                    // Add saved payment method if it is not in the active payment methods list
                    $result[static::KEY_DELETED] = $savedMethod;
                    break;
                }
            }

            if (!isset($savedMethod)) {
                $result[static::KEY_NONE] = static::t('None');
            }
        }

        $result = $result + $list;

        return $result;
    }
}
