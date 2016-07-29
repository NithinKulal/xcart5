<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Blocks\ShippingMethods;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
class Selector extends \XLite\View\ShippingList
{
    /**
     * @return string
     */
    public function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/shipping_methods/';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . 'selector.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'selector.twig';
    }

    /**
     * @return boolean
     */
    public function shouldReload()
    {
        return \XLite\Model\Shipping::getInstance()->hasOnlineProcessors();
    }
}
