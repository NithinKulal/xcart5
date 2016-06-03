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
class ShippingMethods extends \XLite\View\AView
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

        $list[] = $this->getDir() . 'shipping-methods.js';

        return $list;
    }

    public function getListName($field = null)
    {
        $name = 'checkout_fastlane.blocks.shipping_methods';

        if ($field) {
            $name .= '.' . $field;
        }

        return $name;
    }

    /**
     * Check - form is visible or not
     *
     * @return boolean
     */
    protected function isFormVisible()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return 'shipping-methods';
    }

    /**
     * @return void
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'template.twig';
    }
}
