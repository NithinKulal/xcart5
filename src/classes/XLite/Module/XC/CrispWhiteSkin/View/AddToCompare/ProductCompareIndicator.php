<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\AddToCompare;

/**
 * Product comparison widget
 *
 * @Decorator\Depend("XC\ProductComparison")
 */
class ProductCompareIndicator extends \XLite\Module\XC\ProductComparison\View\AddToCompare\ProductCompareIndicator implements \XLite\Base\IDecorator
{
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ProductComparison/header_widget.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = array(
            'file'  => 'modules/XC/ProductComparison/header_widget.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductComparison';
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductComparison/header_indicator.twig';
    }

    protected function getComparedCount()
    {
        return \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->getProductsCount();
    }

    protected function getCompareURL()
    {
        return $this->buildURL('compare');
    }
}
