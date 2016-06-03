<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form;

/**
 * Sort form
 */
class Sort extends \XLite\View\Form\AForm
{
    /**
     * Widget parameter names
     */

    const PARAM_PARAMS = 'params';


    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'order_list';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'search';
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
            self::PARAM_PARAMS => new \XLite\Model\WidgetParam\TypeCollection('Parameters', array()),
        );

        $this->widgetParams[self::PARAM_CLASS_NAME]->setValue('sort-box');
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
     * getFormDefaultParams
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        return $this->getParam(self::PARAM_PARAMS);
    }
}
