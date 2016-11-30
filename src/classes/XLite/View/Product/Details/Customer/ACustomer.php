<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

/**
 * ACustomer
 */
abstract class ACustomer extends \XLite\View\Product\Details\ADetails
{
    /**
     * Get image alternative text
     *
     * @param \XLite\Model\Base\Image $image Image
     * @param integer                 $i     Image index OPTIONAL
     *
     * @return string
     */
    public function getAlt($image, $i = null)
    {
        return $image && $image->getAlt()
            ? $image->getAlt()
            : $this->getProduct()->getName();
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getProduct();
    }

    /**
     * @inheritDoc
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'labels/style.css'
            ]
        );
    }


}
