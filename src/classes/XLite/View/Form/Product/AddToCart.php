<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Product;

/**
 * Add product to cart form
 */
class AddToCart extends \XLite\View\Form\Product\AProduct
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'cart';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'add';
    }

    /**
     * initView
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue($this->getFormDefaultParams());
    }

    /**
     * JavaScript: this value will be returned on form submit
     * NOTE - this function designed for AJAX easy switch on/off
     *
     * @return string
     */
    protected function getOnSubmitResult()
    {
        return 'false';
    }

    /**
     * getFormDefaultParams
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        $result = array();

        $product = $this->getProduct();

        if ($product) {
            $result = array(
                'product_id'  => $product->getProductId(),
                'category_id' => $product->getCategoryId(),
            );
        }

        return $result;
    }

}
