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
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        return parent::defineButtons() + $this->getAdditionalButtons();
    }

    /**
     * Get additional buttons
     *
     * @return array
     */
    protected function getAdditionalButtons()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineAdditionalButtons();
        });
    }

    /**
     * Define additional buttons
     * These buttons will be composed into dropup menu.
     * The divider button is also available: \XLite\View\Button\Dropdown\Divider
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $url = $this->buildURL('images', 'image_resize');

        return [
            $this->getWidget(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL            => 'Generate resized images',
                    \XLite\View\Button\AButton::PARAM_STYLE            => 'action always-enabled',
                    \XLite\View\Button\Tooltip::PARAM_SEPARATE_TOOLTIP => static::t('Generate resized images help text'),
                    \XLite\View\Button\Regular::PARAM_JS_CODE          => 'void(0);',
                    \XLite\View\Button\AButton::PARAM_ATTRIBUTES       => [
                        'data-url' => $url,
                    ],
                ],
                'XLite\View\Button\Tooltip'
            ),
        ];
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass()) . ' images-settings-panel';
    }
}
