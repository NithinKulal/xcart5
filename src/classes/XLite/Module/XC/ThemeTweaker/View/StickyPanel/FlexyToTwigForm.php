<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\StickyPanel;

/**
 * Flexy-to-twig converter items list's sticky panel widget
 */
class FlexyToTwigForm extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = array();

        $list[] = $this->getWidget(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL  => 'Search flexy-templates',
                \XLite\View\Button\AButton::PARAM_STYLE  => 'action search-flexy always-enabled',
                \XLite\View\Button\Regular::PARAM_ACTION => 'search_flexy',
            ),
            '\XLite\View\Button\Regular'
        );

        if ($this->isFlexyTemplatesFound()) {

            $list[] = $this->getWidget(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL  => 'Convert templates',
                    \XLite\View\Button\AButton::PARAM_STYLE  => 'action convert',
                ),
                '\XLite\View\Button\Simple'
            );

            $list[] = $this->getWidget(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL  => 'Remove flexy-templates',
                    \XLite\View\Button\AButton::PARAM_STYLE  => 'action remove-flexy' . ($this->isFlexyTemplatesFound() ? ' always-enabled' : ''),
                    \XLite\View\Button\Regular::PARAM_ACTION => 'remove_flexy',
                    \XLite\View\Button\ConfirmRegular::PARAM_CONFIRM_TEXT => $this->getRemoveConfirmationMessage(),
                ),
                '\XLite\View\Button\ConfirmRegular'
            );
        }

        $list[] = $this->getWidget(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Back to Webmaster mode',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action back always-enabled',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('theme_tweaker_templates'),
            ),
            '\XLite\View\Button\SimpleLink'
        );

        return $list;
    }

    /**
     * Return true if flexy-templates have been found
     *
     * @return boolean
     */
    protected function isFlexyTemplatesFound()
    {
        return (bool) \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->getTemplatesList();
    }

    /**
     * Get CSS class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        $class = trim($class . ' theme-tweaker-flexy2twig-panel');

        return $class;
    }

    /**
     * Get message for 'Remove flexy templates' button
     *
     * @return string
     */
    protected function getRemoveConfirmationMessage()
    {
        return static::t('This action will remove all flexy-templates. Are you sure to proceed?');
    }
}
