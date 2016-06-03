<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Images settings dialog sticky panel
 */
class ImagesSettings extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Cached list of additional buttons
     *
     * @var array
     */
    protected $additionalButtons;

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons() + $this->getAdditionalButtons();

        return $list;
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
        $list = array();

        $url = $this->buildURL('images', 'image_resize');

        $list[] = $this->getWidget(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL            => 'Generate resized images',
                \XLite\View\Button\AButton::PARAM_STYLE            => 'action always-enabled',
                \XLite\View\Button\Tooltip::PARAM_SEPARATE_TOOLTIP => static::t('Generate resized images help text'),
                \XLite\View\Button\Regular::PARAM_JS_CODE          => 'void(0);',
                \XLite\View\Button\AButton::PARAM_ATTRIBUTES       => array(
                    'data-url' => $url,
                ),
            ),
            '\XLite\View\Button\Tooltip'
        );

        return $list;
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        $class = trim($class . ' images-settings-panel');

        return $class;
    }
}
