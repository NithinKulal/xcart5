<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\Button;

/**
 * Add coupon
 */
class AddCoupon extends \XLite\View\Button\Submit
{

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Coupons/button/add_coupon.css';

        return $list;
    }

    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Redeem';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass() . $this->getAddCouponClass());
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getSubmitClass()
    {
        return ' submit';
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getAddCouponClass()
    {
        return ' add-coupon';
    }
}
