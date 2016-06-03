<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\Product\Details;

/**
 * Product details 'Free shipping' label widget
 */
class Label extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-freeship-label';
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/FreeShipping/label/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FreeShipping/label/body.twig';
    }

    /**
     * Return available amount
     *
     * @return integer
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getProduct()->getFreeShip();
    }
}
