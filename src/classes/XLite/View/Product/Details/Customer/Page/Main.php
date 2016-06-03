<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer\Page;

/**
 * Main
 *
 * @ListChild (list="center", zone="customer")
 */
class Main extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'product';

        return $list;
    }


    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/page';
    }

    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        $collection = new \XLite\View\ProductPageCollection(array('product' => $this->getProduct()));
        $collection = $collection->getWidgetsCollection();

        return array(
            'class'                       => array(
                'product-details',
                'product-info-' . $this->getProduct()->getProductId(),
                'box-product',
            ),
            'data-use-widgets-collection' => !empty($collection),
        );
    }

}
