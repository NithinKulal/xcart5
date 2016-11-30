<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Cart widget
 */
abstract class Cart extends \XLite\View\Cart implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file'  => 'css/less/cart.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];
        $list[] = [
            'file'  => 'css/less/estimator.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Check - discount coupon subpanel is visible or not
     *
     * @param array $surcharge Surcharge
     *
     * @return boolean
     */
    protected function isShippingEstimatorVisible(array $surcharge)
    {
        return 'shipping' === strtolower($surcharge['code']);
    }
}
