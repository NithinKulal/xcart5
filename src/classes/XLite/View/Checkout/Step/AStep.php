<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Checkout\Step;

/**
 * Abstract checkout step widget
 */
abstract class AStep extends \XLite\View\AView
{
    /**
     * Common widget parameter names
     */
    const PARAM_PARENT_WIDGET = 'parentWidget';

    /**
     * Get step name
     *
     * @return string
     */
    abstract public function getStepName();

    /**
     * Get step title
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Get steps collector
     *
     * @return \XLite\View\Checkout\Steps
     */
    public function getStepsCollector()
    {
        return $this->getParam(self::PARAM_PARENT_WIDGET);
    }

    /**
     * Check - step is enabled (true) or skipped (false)
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return true;
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
            self::PARAM_PARENT_WIDGET => new \XLite\Model\WidgetParam\TypeObject('Parent widget', null, false, '\XLite\View\Checkout\Steps'),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/body.twig';
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->getParam(self::PARAM_TEMPLATE) == $this->getDefaultTemplate()
            ? $this->getStepTemplate()
            : $this->getParam(self::PARAM_TEMPLATE);
    }

    /**
     * Get step template
     *
     * @return string
     */
    protected function getStepTemplate()
    {
        $path = 'checkout/steps/' . $this->getStepName() . '/';

        if (!$this->isEnabled()) {
            $path .= 'disabled.twig';

        } else {
            $path .= 'selected.twig';
        }

        return $path;
    }

}
