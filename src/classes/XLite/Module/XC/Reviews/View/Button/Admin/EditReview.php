<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Button\Admin;

/**
 * Edit review popup button
 */
class EditReview extends \XLite\View\Button\APopupButton
{
    /**
     * Widget param names
     */
    const PARAM_ID                  = 'id';
    const PARAM_TARGET_PRODUCT_ID   = 'target_product_id';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/Reviews/button/js/edit_review/func.js';
        $list[] = 'modules/XC/Reviews/button/js/edit_review/controller.js';

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
            static::PARAM_ID => new \XLite\Model\WidgetParam\TypeInt('Review Id', 0),
            static::PARAM_TARGET_PRODUCT_ID => new \XLite\Model\WidgetParam\TypeInt('Target product id', 0),
        );
    }

    /**
     * Return review id which is provided to the widget
     *
     * @return string
     */
    protected function getId()
    {
        return $this->getParam(static::PARAM_ID);
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
            'target'    => 'review',
            'widget'    => '\XLite\Module\XC\Reviews\View\Review',
            'id'        => $this->getId(),
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
        return '';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . 'edit-review';
    }
}
