<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Popup;

/**
 * Abstract popup-based inline field
 */
abstract class APopup extends \XLite\View\FormField\Inline\Base\Single
{

    /**
     * Get popup widget 
     * 
     * @return string
     */
    abstract protected function getPopupWidget();

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (!$this->getViewOnly()) {
            $list[] = 'form_field/inline/popup/popup.js';
        }

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'form_field/inline/popup/popup.css';

        return $list;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-popup';
    }

    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        $list = parent::getContainerAttributes();

        $list['data-popup-url'] = static::buildURL(
            $this->getPopupTarget(),
            null,
            array('widget' => $this->getPopupWidget()) + $this->getPopupParameters()
        );

        return $list;
    }

    /**
     * Get popup target 
     * 
     * @return string
     */
    protected function getPopupTarget()
    {
        return \XLite::TARGET_DEFAULT;
    }

    /**
     * Get popup parameters 
     * 
     * @return array
     */
    protected function getPopupParameters()
    {
        return array();
    }

}
