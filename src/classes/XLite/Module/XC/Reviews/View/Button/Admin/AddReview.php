<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Button\Admin;

/**
 * Add review popup button
 */
class AddReview extends \XLite\View\Button\APopupButton
{
    /**
     * Widget param names
     */
    const PARAM_TARGET_PRODUCT_ID = 'target_product_id';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/Reviews/button/js/add_review/func.js';
        $list[] = 'modules/XC/Reviews/button/js/add_review/controller.js';

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
            static::PARAM_TARGET_PRODUCT_ID => new \XLite\Model\WidgetParam\TypeInt('', 0),
        );
    }

    /**
     * Return target product id which is provided to the widget
     *
     * @return string
     */
    protected function getTargetProductId()
    {
        return $this->getParam(static::PARAM_TARGET_PRODUCT_ID);
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $params = array(
            'target' => 'review',
            'widget' => '\XLite\Module\XC\Reviews\View\Review',
        );

        if ($this->getTargetProductId()) {
            $params[self::PARAM_TARGET_PRODUCT_ID] = $this->getTargetProductId();
        }

        return $params;
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Add review';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' add-review';
    }
}
