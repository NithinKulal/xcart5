<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Form;

/**
 * Rate product form
 */
class AverageRating extends \XLite\View\Form\AForm
{
    /**
     * Widget params names
     */
    const PARAM_PRODUCT_ID      = 'product_id';
    const PARAM_RETURN_TARGET   = 'return_target';
    const PARAM_TARGET_WIDGET   = 'target_widget';

    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'rate';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = array(
            self::PARAM_RETURN_TARGET   => \XLite::getController()->getTarget(),
        );

        return $params;
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
            self::PARAM_PRODUCT_ID      => new \XLite\Model\WidgetParam\TypeInt('Product Id', 0),
            self::PARAM_RETURN_TARGET   => new \XLite\Model\WidgetParam\TypeString('Return target', '', false),
            self::PARAM_TARGET_WIDGET   => new \XLite\Model\WidgetParam\TypeString('Target widget', '', false),
        );
    }

    /**
     * Called before the includeCompiledFile()
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue(
            array(
                self::PARAM_PRODUCT_ID      => $this->getParam(static::PARAM_PRODUCT_ID),
                self::PARAM_TARGET_WIDGET   => $this->getParam(static::PARAM_TARGET_WIDGET),
            )
        );
    }
}
