<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Settings dialog model widget
 */
class ModuleSettings extends \XLite\View\Model\Settings
{
    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $returnTarget = \XLite\Core\Request::getInstance()->returnTarget;

        if ('layout' === $returnTarget) {
            $result['layout'] = new \XLite\View\Button\SimpleLink(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to layout settings'),
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action addons-list-back-button',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('layout'),
                )
            );
        } else {
            $result['addons-list'] = new \XLite\View\Button\BackToModulesLink(
                array(
                    \XLite\View\Button\BackToModulesLink::PARAM_MODULE_ID => \XLite\Core\Request::getInstance()->moduleId,
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action addons-list-back-button',
                )
            );
        }

        return $result;
    }
}
