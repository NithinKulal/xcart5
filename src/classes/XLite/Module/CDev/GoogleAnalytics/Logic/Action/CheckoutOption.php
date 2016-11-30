<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

/**
 * Class CheckoutOption
 */
class CheckoutOption implements IAction
{
    protected $option;
    protected $step;

    /**
     * CheckoutOption constructor.
     *
     * @param string  $option
     * @param integer $step
     *
     * @internal param array $data
     */
    public function __construct($option, $step = null)
    {
        $this->option = $option;
        $this->step = $step;
    }

    /**
     * @return bool
     */
    public function isApplicable()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
            && \XLite::getController() instanceof \XLite\Controller\Customer\Checkout;
    }

    /**
     * @return array
     */
    public function getActionData()
    {
        $result = [
            'ga-type'   => 'checkout-option',
            'ga-action' => 'checkout',
            'data'      => $this->getCheckoutOptionActionData()
        ];

        return $result;
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @return array
     */
    protected function getCheckoutOptionActionData()
    {
        $data = [
            'option' => $this->option,
        ];

        if ($this->step) {
            $data['step'] = $this->step;
        }

        return $data;
    }
}