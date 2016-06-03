<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment\Processor;

/**
 * Common offline processor
 */
class Offline extends \XLite\Model\Payment\Base\Processor
{
    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return 'payment/offline.twig';
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        return static::PENDING;
    }

}
