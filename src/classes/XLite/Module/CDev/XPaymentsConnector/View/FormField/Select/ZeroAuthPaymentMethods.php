<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\FormField\Select;

/**
 * Selector for zero-dollar authorization payment method
 */
class ZeroAuthPaymentMethods extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $result = array(
            \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::DISABLED => \XLite\Core\Translation::lbl('Do not use card setup'),
        );

        $result += \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getCanSaveCardsMethods(true);

        return $result;
    }

    /**
     * Get field label
     *
     * @return string
     */
    public function getLabel()
    {
        return \XLite\Core\Translation::lbl('Payment method for card setup');
    }

    /**
     * Get default name
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return 'method_id';
    }

}
