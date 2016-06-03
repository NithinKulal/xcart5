<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Page;

/**
 * Coupons promotion page view
 */
class Coupons extends \XLite\View\AView
{
    /**
     * Returns CSS style files
     *
     * @return string
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'page/coupons/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'page/coupons/body.twig';
    }

    /**
     * Returns purchase license URL
     *
     * @return string
     */
    protected function getPurchaseLicenseURL()
    {
        return \XLite\Core\Marketplace::getPurchaseURL();
    }
}
