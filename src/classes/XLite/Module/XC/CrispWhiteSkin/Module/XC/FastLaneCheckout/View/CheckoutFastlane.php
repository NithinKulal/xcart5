<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Module\XC\FastLaneCheckout\View;

/**
 * Class CheckoutFastlane
 *
 * @Decorator\Depend("XC\FastLaneCheckout")
 */
class CheckoutFastlane extends \XLite\Module\XC\FastLaneCheckout\View\CheckoutFastlane implements \XLite\Base\IDecorator
{
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file'  => 'css/less/checkout.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];
        $list[] = [
            'file'  => 'css/less/address-book.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}