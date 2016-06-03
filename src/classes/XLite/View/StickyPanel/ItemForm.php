<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel form item-based form
 */
class ItemForm extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Buttons list (cache)
     *
     * @var array
     */
    protected $buttonsList;

    /**
     * Get buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        if (!isset($this->buttonsList)) {
            $this->buttonsList = $this->defineButtons();
        }

        return $this->buttonsList;
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = array();
        $list['save'] = $this->getSaveWidget();

        return $list;
    }

    /**
     * Get "save" widget
     *
     * @return \XLite\View\Button\Submit
     */
    protected function getSaveWidget()
    {
        return $this->getWidget(
            array(
                'style'    => 'action submit',
                'label'    => $this->getSaveWidgetLabel(),
                'disabled' => true,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => $this->getSaveWidgetStyle(),
            ),
            'XLite\View\Button\Submit'
        );
    }

    /**
     * Defines the label for the save button
     *
     * @return string
     */
    protected function getSaveWidgetLabel()
    {
        return static::t('Save changes');
    }

    /**
     * Defines the style for the save button
     *
     * @return string
     */
    protected function getSaveWidgetStyle()
    {
        return 'regular-main-button';
    }
}
