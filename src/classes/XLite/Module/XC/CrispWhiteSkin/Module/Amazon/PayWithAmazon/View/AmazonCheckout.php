<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace XLite\Module\XC\CrispWhiteSkin\Module\Amazon\PayWithAmazon\View;

/**
 * @Decorator\Depend ("Amazon\PayWithAmazon")
 */
class AmazonCheckout extends \XLite\Module\Amazon\PayWithAmazon\View\AmazonCheckout implements \XLite\Base\IDecorator
{
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'css/less/checkout.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
