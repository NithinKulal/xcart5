<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Theme tweaker templates page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ThemeTweakerTemplates extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['theme_tweaker_templates']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/theme_tweaker_templates/body.twig';
    }

    /**
     * Check - search box is visible or not
     *
     * @return boolean
     */
    protected function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')->count();
    }

    /**
     * Return true if flexy-templates have been found
     *
     * @return boolean
     */
    protected function hasFlexyTemplates()
    {
        return (bool) \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->getTemplatesList();
    }
}
