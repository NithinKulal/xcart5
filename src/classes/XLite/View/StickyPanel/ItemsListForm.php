<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel form items list-based form
 */
class ItemsListForm extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Additional buttons (cache)
     *
     * @var array
     */
    protected $additionalButtons;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'js/stickyPanelModelList.js';

        return $list;
    }

    /**
     * Check panel has more actions buttons
     *
     * @return boolean 
     */
    protected function hasMoreActionsButtons()
    {
        return true;
    }

    /**
     * Should more actions buttons be disabled?
     *
     * @return boolean 
     */
    protected function isMoreActionsDisabled()
    {
        return $this->hasMoreActionsButtons();
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        if ($this->getAdditionalButtons()) {
            $list['additional'] = $this->getWidget(
                array(
                    'template' => 'items_list/model/additional_buttons.twig',
                )
            );
        }

        return $list;
    }

    /**
     * Flag to display OR label
     *
     * @return boolean
     */
    protected function isDisplayORLabel()
    {
        return true;
    }

    /**
     * Returns "more actions" specific label
     *
     * @return string
     */
    protected function getMoreActionsText()
    {
        return static::t('More actions for selected');
    }

    /**
     * Returns "more actions" specific label for bubble context window
     *
     * @return string
     */
    protected function getMoreActionsPopupText()
    {
        return static::t('More actions for selected');
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
     * These buttons will be composed into dropup menu.
     * The divider button is also available: \XLite\View\Button\Divider
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return array();
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        $class = trim($class . ' model-list');

        if ($this->getAdditionalButtons()) {
            $class = trim($class . ' has-add-buttons');
        }

        return $class;
    }
}
