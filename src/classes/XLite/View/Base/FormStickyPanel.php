<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Base;

/**
 * Form-based sticky panel
 */
abstract class FormStickyPanel extends \XLite\View\Base\StickyPanel
{
    /**
     * Get buttons widgets
     *
     * @return array
     */
    abstract protected function getButtons();

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'form/panel';
    }

    /**
     * Get cell attributes 
     * 
     * @param integer           $idx    Cell index
     * @param string            $name   Cell name
     * @param \XLite\View\AView $button Button
     *  
     * @return array
     */
    protected function getCellAttributes($idx, $name, \XLite\View\AView $button)
    {
        return array(
            'class' => $this->getCellClass($idx, $name, $button),
        );
    }

    /**
     * Get cell class
     *
     * @param integer           $idx    Button index
     * @param string            $name   Button name
     * @param \XLite\View\AView $button Button
     *
     * @return string
     */
    protected function getCellClass($idx, $name, \XLite\View\AView $button)
    {
        $classes = array('panel-cell', $name);

        if (1 == $idx) {
            $classes[] = 'first';
        }

        if (count($this->getButtons()) == $idx) {
            $classes[] = 'last';
        }


        return implode(' ', $classes);
    }

    /**
     * Get subcell class (additional buttons)
     *
     * @param integer           $idx    Button index
     * @param string            $name   Button name
     * @param \XLite\View\AView $button Button
     *
     * @return string
     */
    protected function getSubcellClass($idx, $name, \XLite\View\AView $button)
    {
        $classes = array('panel-subcell', $name);

        if (1 == $idx) {
            $classes[] = 'first';
        }

        if (count($this->getAdditionalButtons()) == $idx) {
            $classes[] = 'last';
        }


        return implode(' ', $classes);
    }

    /**
     * Check - sticky panel is active only if form is changed
     *
     * @return boolean
     */
    protected function isFormChangeActivation()
    {
        return true;
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        if ($this->isFormChangeActivation()) {
            $class .= ' form-change-activation';
        } else {
            $class .= ' form-do-not-change-activation';
        }

        return $class;
    }

}
