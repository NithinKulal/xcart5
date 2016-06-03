<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Blocks;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
class PaymentMethods extends \XLite\View\AView
{
    /**
     * @return string
     */
    public function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/payment_methods/';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = array(
            'file'  => $this->getDir() . 'style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . 'payment-methods.js';

        return $list;
    }

    /**
     * @return void
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'template.twig';
    }
}
