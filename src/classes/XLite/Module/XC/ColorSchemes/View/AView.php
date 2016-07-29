<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ColorSchemes\View;

/**
 * Color schemes adds
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Return theme common files
     *
     * @param boolean $adminZone Admin zone flag OPTIONAL
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        if (!(null === $adminZone ? \XLite::isAdminZone() : $adminZone)) {
            $list[static::RESOURCE_CSS][] = \XLite\Module\XC\ColorSchemes\Main::getColorSchemeCSS();

            if (!\XLite\Module\XC\ColorSchemes\Main::isDefaultColorScheme()) {
                $list[static::RESOURCE_CSS][] = array(
                    'file' => \XLite\Module\XC\ColorSchemes\Main::getColorSchemeLess(),
                    'media' => 'screen',
                    'merge' => 'bootstrap/css/bootstrap.less',
                );
            }
        }

        return $list;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $result = parent::getCacheParameters();

        if (!\XLite::isAdminZone()) {
            $result[] = \XLite\Module\XC\ColorSchemes\Main::getSkinName();
        }

        return $result;
    }
}
