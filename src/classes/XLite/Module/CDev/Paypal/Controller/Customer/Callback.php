<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

use \XLite\Module\CDev\Paypal;

/**
 * Checkout controller
 */
class Callback extends \XLite\Controller\Customer\Callback implements \XLite\Base\IDecorator
{
    protected $ignore = false;

    /**
     * @var \XLite\Model\Payment\Transaction
     */
    protected $transaction;

    /**
     * Process callback
     */
    protected function doActionCallback()
    {
        $this->detectTransaction();

        if (!$this->ignore) {
            parent::doActionCallback();
            if ($this->transaction && $this->transaction->isByPayPal()) {
                Paypal\Core\Lock\OrderLocker::getInstance()->unlock($this->transaction->getOrder());
            }
        }
    }

    /**
     * Detect transaction from request or from the inner payment methods detection
     *
     * @return \XLite\Model\Payment\Transaction
     */
    protected function detectTransaction()
    {
        if (null === $this->transaction) {
            $this->transaction = parent::detectTransaction();
            if ($this->transaction && $this->transaction->isByPayPal()) {
                $order = $this->transaction->getOrder();
                if (Paypal\Core\Lock\OrderLocker::getInstance()->isLocked($order)) {
                    $this->ignore = true;

                } else {
                    Paypal\Core\Lock\OrderLocker::getInstance()->lock($order);
                }
            }
        }

        return $this->transaction;
    }
}
