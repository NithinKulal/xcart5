<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Flexy-to-twig converter page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class FlexyToTwig extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'flexy_to_twig';

        return $list;
    }

    /**
     * Returns JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->hasFlexyTemplates()) {
            $list[] = 'modules/XC/ThemeTweaker/flexy_to_twig/controller.js';
            $list[] = 'modules/XC/ThemeTweaker/flexy_to_twig/flexy_to_twig.js';
        }

        return $list;
    }

    /**
     * Returns CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ThemeTweaker/flexy_to_twig/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/flexy_to_twig/body.twig';
    }

    /**
     * Return true if flexy-templates has been found
     *
     * @return boolean
     */
    protected function hasFlexyTemplates()
    {
        return (bool) \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->getTemplatesList();
    }

    /**
     * Return true if orphan templates found
     *
     * @return boolean
     */
    protected function hasOrphans()
    {
        return \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->hasOrphans();
    }
}
