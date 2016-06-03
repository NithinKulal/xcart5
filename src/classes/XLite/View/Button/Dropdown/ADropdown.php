<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * Abstract dropdown button
 */
class ADropdown extends \XLite\View\Button\AButton
{
    /**
     * Additional buttons (cache)
     *
     * @var array
     */
    protected $additionalButtons;

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/dropdown.js';

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
        $list[] = 'button/css/dropdown.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/dropdown.twig';
    }

    /**
     * Get additional buttons
     *
     * @return array
     */
    protected function getAdditionalButtons()
    {
        if (!isset($this->additionalButtons)) {
            $this->additionalButtons = $this->defineAdditionalButtons();
        }

        return $this->additionalButtons;
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return array();
    }

    /**
     * Get style 
     * 
     * @return string
     */
    protected function getStyle()
    {
        return 'dropdown'
            . ($this->getAdditionalButtons() ? ' not-round' : '')
            . ($this->getParam(self::PARAM_STYLE) ? ' ' . $this->getParam(self::PARAM_STYLE) : '');
    }
}
