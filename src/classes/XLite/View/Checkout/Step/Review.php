<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Checkout\Step;

/**
 * Review checkout step
 */
class Review extends \XLite\View\Checkout\Step\AStep
{
    /**
     * Get step name
     *
     * @return string
     */
    public function getStepName()
    {
        return 'review';
    }

    /**
     * Get step title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Order review';
    }

    /**
     * Check - step is complete or not
     *
     * @return boolean
     */
    public function isCompleted()
    {
        return false;
    }

    /**
     * Get Terms and Conditions page URL
     *
     * @return string
     */
    public function getTermsURL()
    {
        return \XLite\Core\Config::getInstance()->General->terms_url;
    }

    /**
     * Get modifier
     *
     * @return string
     */
    protected function getPaymentMethodName()
    {
        $pm = $this->getCart()->getPaymentMethod();

        return $pm ? $pm->getName() : '';
    }
}
