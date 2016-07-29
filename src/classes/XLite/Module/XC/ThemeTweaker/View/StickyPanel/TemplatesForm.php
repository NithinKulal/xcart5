<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\StickyPanel;

/**
 * Templates sticky panel widget
 */
class TemplatesForm extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        return parent::defineButtons() + $this->defineMoreButtons();
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineMoreButtons()
    {
        $list = array();

        if (\XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->getTemplatesList()) {
            $list[] = $this->getWidget(
                array(
                    \XLite\View\Button\SimpleLink::PARAM_LABEL    => 'Flexy to twig converter',
                    \XLite\View\Button\SimpleLink::PARAM_STYLE    => 'action flexy2twig always-enabled',
                    \XLite\View\Button\SimpleLink::PARAM_LOCATION => $this->buildURL('flexy_to_twig'),
                ),
                '\XLite\View\Button\SimpleLink'
            );
        }

        return $list;
    }

    /**
     * Get CSS class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        $class = trim($class . ' theme-tweaker-templates-panel');

        return $class;
    }
}
