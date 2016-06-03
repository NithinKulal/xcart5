<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\ItemsList\ProductSelection;

/**
 * Product selections list table form
 */
class Table extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product_selections';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            \XLite\View\Button\PopupProductSelector::PARAM_REDIRECT_URL => new \XLite\Model\WidgetParam\TypeString(
                'URL to redirect to',
                \XLite\Core\Request::getInstance()->{\XLite\View\Button\PopupProductSelector::PARAM_REDIRECT_URL}
            ),
        );
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue($this->getFormDefaultParams());
    }

    /**
     * Get form default parameters
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        return array(
            \XLite\View\Button\PopupProductSelector::PARAM_REDIRECT_URL
                => $this->getParam(\XLite\View\Button\PopupProductSelector::PARAM_REDIRECT_URL),
        );
    }
}