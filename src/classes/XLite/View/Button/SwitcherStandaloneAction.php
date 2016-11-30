<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Switcher button
 */
class SwitcherStandaloneAction extends \XLite\View\Button\AButton
{
    const PARAM_TARGET = 'target';
    const PARAM_ACTION = 'action';

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/switcher-standalone.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/switcher_standalone.twig';
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
            self::PARAM_TARGET       => new \XLite\Model\WidgetParam\TypeString('Action target', ''),
            self::PARAM_ACTION       => new \XLite\Model\WidgetParam\TypeString('Action action', ''),
        );
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultLabel()
    {
        return 'Switch';
    }

    /**
     * @return array
     */
    public function getCommentedData()
    {
        return [
            'url'   => $this->buildURL(
                $this->getParam(static::PARAM_TARGET),
                $this->getParam(static::PARAM_ACTION)
            ),
            'data'  => [],
        ];
    }
}
