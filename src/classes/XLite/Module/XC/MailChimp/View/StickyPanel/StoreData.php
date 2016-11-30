<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\StickyPanel;

/**
 * Class StoreData
 */
class StoreData extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Defines the label for the approve button
     *
     * @return string
     */
    protected function getSaveWidgetLabel()
    {
        return static::t('Update');
    }


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
     * Define additional buttons
     * These buttons will be composed into dropup menu.
     * The divider button is also available: \XLite\View\Button\Dropdown\Divider
     *
     * @return array
     */
    protected function getAdditionalButtons()
    {
        return [
            $this->getWidget(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL     => static::t('Upload store data to MailChimp'),
                    \XLite\View\Button\AButton::PARAM_STYLE     => 'regular-button always-enabled',
                    \XLite\View\Button\Regular::PARAM_ACTION    => 'startUploadAll'
                ],
                'XLite\View\Button\Regular'
            ),
        ];
    }
}