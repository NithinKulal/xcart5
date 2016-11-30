<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * IntegrityCheck sticky panel
 */
class IntegrityCheck extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        $class = trim($class . ' integrity-check-panel');

        return $class;
    }

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
        $list['refresh'] = $this->getSaveWidget();

        return $list;
    }

    /**
     * Get "save" widget
     *
     * @return \XLite\View\AView
     */
    protected function getSaveWidget()
    {
        return $this->getWidget(
            array(
                'style'    => 'btn regular-main-button refresh',
                'label'    => static::t('Refresh integrity status'),
                \XLite\View\Button\Link::PARAM_LOCATION => static::buildURL(
                    'integrity_check',
                    'start'
                ),
                'disabled' => false,
            ),
            'XLite\View\Button\SimpleLink'
        );
    }
}
