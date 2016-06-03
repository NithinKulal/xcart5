<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Button\Customer;

/**
 * Add review button widget
 *
 */
class AddReview extends \XLite\View\Button\APopupButton
{
    /*
     * Widget param names
     */
    const PARAM_PRODUCT = 'product';

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/Reviews/button/js/add_review.js';

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
        );
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }

    /**
     * Get productid
     *
     * @return integer
     */
    protected function getProductId()
    {
        return $this->getProduct()
            ? $this->getProduct()->getProductId()
            : null;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target'        => 'review',
            'product_id'    => $this->getProductId(),
            'return_target' => \XLite\Core\Request::getInstance()->target,
            'widget'        => '\XLite\Module\XC\Reviews\View\Customer\ModifyReview',
        );
    }

    /**
     * Return CSS class
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' add-review ';
    }
}
