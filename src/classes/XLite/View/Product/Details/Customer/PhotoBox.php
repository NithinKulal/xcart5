<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

/**
 * PhotoBox
 *
 * @ListChild (list="product.details.page.image", weight="0")
 */
class PhotoBox extends \XLite\View\Product\Details\Customer\ACustomer
{
    /**
     * Return a list of CSS files required to display the widget
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/parts/page.image.photo.css';

        return $list;
    }


    /**
     * Return the default widget template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/parts/page.image.photo.twig';
    }
}
