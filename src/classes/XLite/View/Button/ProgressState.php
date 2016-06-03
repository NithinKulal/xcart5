<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Progress state button
 */
class ProgressState extends \XLite\View\Button\AButton
{
    /**
     * Widget parameters to use
     */
    const PARAM_STATE   = 'state';
    const PARAM_JS_CODE = 'jsCode';

    const STATE_STILL       = 'still';
    const STATE_IN_PROGRESS = 'in_progress';
    const STATE_SUCCESS     = 'success';
    const STATE_FAIL        = 'fail';

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/progress-state.js';

        return $list;
    }

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'button/css/progress-state.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/progress-state.twig';
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
            static::PARAM_STATE   => new \XLite\Model\WidgetParam\TypeString('Initial state', static::STATE_STILL),
            static::PARAM_JS_CODE => new \XLite\Model\WidgetParam\TypeString('JS code', null, true),
        );
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass()
            . ' progress-state'
            . ' ' . $this->getParam(static::PARAM_STATE);
    }

    /**
     * JavaScript: return specified (or default) JS code to execute
     *
     * @return string
     */
    protected function getJSCode()
    {
        return $this->getParam(static::PARAM_JS_CODE);
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();

        return array_merge($list, $this->getLinkAttributes());
    }

    /**
     * Onclick specific attribute is added
     *
     * @return array
     */
    protected function getLinkAttributes()
    {
        return $this->getJSCode()
            ? array('onclick' => 'javascript: ' . $this->getJSCode())
            : array();
    }
}
