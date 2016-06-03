<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Theme tweaker template page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ThemeTweakerTemplate extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'theme_tweaker_template';

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
        if ($this->isCreate()) {
            $list[] = 'modules/XC/ThemeTweaker/theme_tweaker_template/create.css';
        }

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/theme_tweaker_template/body.twig';
    }
}