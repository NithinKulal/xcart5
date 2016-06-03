<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model;

/**
 * Order
 */
class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Returns true if the order has any pin codes
     *
     * @return boolean
     */
    public function hasPinCodes()
    {
        $result = false;

        foreach ($this->getItems() as $item) {
            if ($item->countPinCodes() || $item->getProduct()->getPinCodesEnabled()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Assign PIN codes to the order items
     *
     * @return void
     */
    public function acquirePINCodes()
    {
        $missingCount = 0;
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()->getPinCodesEnabled() && !$item->countPinCodes()) {
                $item->acquirePinCodes();
                $missingCount += $item->countMissingPinCodes();
            }
        }

        if ($missingCount) {
             \XLite\Core\Mailer::sendAcquirePinCodesFailedAdmin($this);
             \XLite\Core\TopMessage::addError(
                 'Could not assign X PIN codes to order #Y.',
                 array(
                     'count'   => $missingCount,
                     'orderId' => $this->getOrderNumber(),
                 )
             );
        }
    }

    /**
     * Process PIN codes 
     * 
     * @return void
     */
    public function processPINCodes()
    {
        $missingCount = 0;
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()->getPinCodesEnabled()) {
                if (!$item->countPinCodes()) {
                    $item->acquirePinCodes();
                    $missingCount += $item->countMissingPinCodes();
                }

                if ($item->countPinCodes()) {
                    $item->salePinCodes();
                }
            }
        }

        if ($missingCount) {
             \XLite\Core\Mailer::getInstance()->sendAcquirePinCodesFailedAdmin($this);
             \XLite\Core\TopMessage::addError(
                 'Could not assign X PIN codes to order #Y.',
                 array(
                     'count'   => $missingCount,
                     'orderId' => $this->getOrderNumber(),
                 )
             );
        }
    }

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        $this->acquirePINCodes();

        parent::processSucceed();
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processProcess()
    {
        $this->processPINCodes();

        parent::processProcess();
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processDeclinePIN()
    {
        $this->releasePINCodes();

        if ($this->hasPinCodes()) {
            parent::processDecline();
        }
    }

    /**
     * Release PIN codes linked to order items
     *
     * @return void
     */
    protected function releasePINCodes()
    {
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()->getPinCodes()) {
                $item->releasePINCodes();
            }
        }
    }
}
